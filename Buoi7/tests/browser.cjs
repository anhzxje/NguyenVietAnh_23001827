// Chạy với Brave/Chromium: BROWSER_BIN=/path/to/browser node Buoi7/tests/browser.cjs
// VUE_JS có thể trỏ tới bản vue.global.js đã tải để kiểm tra không cần mạng.
const assert = require('node:assert/strict');
const { spawn } = require('node:child_process');
const fs = require('node:fs/promises');
const os = require('node:os');
const path = require('node:path');
const { pathToFileURL } = require('node:url');

(async () => {
    const dir = await fs.mkdtemp(path.join(os.tmpdir(), 'buoi7-browser-'));
    let browser;
    const pending = new Map();
    const deadline = setTimeout(() => {
        for (const { reject } of pending.values()) reject(new Error('Trình duyệt không phản hồi'));
    }, 20000);
    try {
        const root = path.resolve(__dirname, '..');
        const vue = process.env.VUE_JS ? await fs.readFile(process.env.VUE_JS) :
            Buffer.from(await (await fetch('https://unpkg.com/vue@3.5.13/dist/vue.global.js')).arrayBuffer());
        await fs.writeFile(path.join(dir, 'vue.js'), vue);
        let html = await fs.readFile(path.join(root, 'Bai1.html'), 'utf8');
        html = html.replace('https://unpkg.com/vue@3.5.13/dist/vue.global.js', 'vue.js')
            .replace(/src="bai1\.js[^\"]*"/, `src="${pathToFileURL(path.join(root, 'bai1.js'))}"`)
            .replace('href="style.css"', `href="${pathToFileURL(path.join(root, 'style.css'))}"`);
        await fs.writeFile(path.join(dir, 'test.html'), html);
        browser = spawn(process.env.BROWSER_BIN || '/opt/brave.com/brave/brave', [
            '--headless', '--no-sandbox', '--disable-gpu', '--disable-dev-shm-usage',
            '--disable-extensions', '--no-first-run', '--disable-background-networking',
            '--allow-file-access-from-files', '--remote-debugging-pipe',
            `--user-data-dir=${path.join(dir, 'profile')}`, 'about:blank'
        ], { stdio: ['ignore', 'ignore', 'ignore', 'pipe', 'pipe'] });
        let id = 0, buffer = '';
        browser.stdio[4].on('data', chunk => {
            buffer += chunk;
            let end;
            while ((end = buffer.indexOf('\0')) >= 0) {
                const message = JSON.parse(buffer.slice(0, end));
                buffer = buffer.slice(end + 1);
                if (pending.has(message.id)) {
                    const waiter = pending.get(message.id);
                    pending.delete(message.id);
                    message.error ? waiter.reject(new Error(message.error.message)) : waiter.resolve(message.result);
                }
            }
        });
        const call = (method, params = {}, sessionId) => new Promise((resolve, reject) => {
            pending.set(++id, { resolve, reject });
            browser.stdio[3].write(JSON.stringify({ id, method, params, sessionId }) + '\0');
        });
        const { targetId } = await call('Target.createTarget', { url: 'about:blank' });
        const { sessionId } = await call('Target.attachToTarget', { targetId, flatten: true });
        const evaluate = async expression => {
            const response = await call('Runtime.evaluate', { expression, awaitPromise: true, returnByValue: true }, sessionId);
            if (response.exceptionDetails) throw new Error(response.exceptionDetails.exception.description);
            return response.result.value;
        };
        await call('Page.navigate', { url: pathToFileURL(path.join(dir, 'test.html')).href }, sessionId);
        // Đợi điều kiện mount thay vì chỉ dựa vào thời gian tải trang.
        for (let n = 0; n < 100; n++) {
            try { if (await evaluate(`!!document.querySelector('#app')?.__vue_app__`)) break; } catch {}
            await new Promise(resolve => setTimeout(resolve, 30));
        }
        // Nội dung đã hiện trong ô phải sống qua lần cập nhật Vue kể cả khi
        // bộ gõ/autofill chưa phát input trước thao tác chọn danh mục bằng chuột.
        assert.deepEqual(await evaluate(`(async()=>{
            const inputs=document.querySelectorAll('form input');
            for(const [index,value] of [[0,'Nội dung đang nhập'],[1,'45000'],[2,'5']]) inputs[index].value=value;
            const select=document.querySelector('form select');select.value='Phụ kiện';
            select.dispatchEvent(new Event('change',{bubbles:true}));await Vue.nextTick();
            return Array.from(inputs,el=>el.value);
        })()`), ['Nội dung đang nhập','45000','5'], 'Cập nhật form không được xóa nội dung đang hiện trong ô');
        assert.equal(await evaluate(`(async()=>{document.querySelector('form').requestSubmit();await Vue.nextTick();return document.querySelectorAll('tbody tr').length})()`), 7, 'Gửi form phải dùng dữ liệu đang hiện dù chưa phát input');
        const click = async selector => {
            const point = await evaluate(`(()=>{const el=document.querySelector(${JSON.stringify(selector)});el.scrollIntoView({block:'center'});const r=el.getBoundingClientRect();return {x:r.x+r.width/2,y:r.y+r.height/2}})()`);
            await call('Input.dispatchMouseEvent', { type: 'mousePressed', button: 'left', clickCount: 1, ...point }, sessionId);
            await call('Input.dispatchMouseEvent', { type: 'mouseReleased', button: 'left', clickCount: 1, ...point }, sessionId);
        };
        for (const [selector, text, next] of [
            ['form input[placeholder="Nhập tên"]', 'Sản phẩm bấm chuột', 'form input[inputmode="decimal"]'],
            ['form input[inputmode="decimal"]', '60000', 'form input[inputmode="numeric"]'],
            ['form input[inputmode="numeric"]', '2', 'form input[placeholder="Nhập tên"]']
        ]) {
            await click(selector);
            await call('Input.imeSetComposition', { text, selectionStart: text.length, selectionEnd: text.length }, sessionId);
            await click(next);
        }
        assert.deepEqual(await evaluate(`Array.from(document.querySelectorAll('form input'),el=>el.value)`), ['Sản phẩm bấm chuột', '60000', '2'], 'Bấm chuột chuyển ô phải giữ cả ba giá trị');
        await click('form button');
        assert.equal(await evaluate(`document.querySelectorAll('tbody tr').length`), 8);
        // Nhập qua sự kiện bàn phím của trình duyệt và rời ô bằng Tab.
        for (const [index, text] of [[0, 'Thử nhập bình thường'], [1, '50000'], [2, '3']]) {
            await evaluate(`document.querySelectorAll('form input')[${index}].focus()`);
            await call('Input.insertText', { text }, sessionId);
            await call('Input.dispatchKeyEvent', { type: 'keyDown', key: 'Tab', code: 'Tab', windowsVirtualKeyCode: 9 }, sessionId);
            await call('Input.dispatchKeyEvent', { type: 'keyUp', key: 'Tab', code: 'Tab', windowsVirtualKeyCode: 9 }, sessionId);
        }
        assert.deepEqual(await evaluate(`Array.from(document.querySelectorAll('form input'), el=>el.value)`), ['Thử nhập bình thường', '50000', '3']);
        assert.equal(await evaluate(`(async()=>{document.querySelector('form button').click();await Vue.nextTick();return document.querySelectorAll('tbody tr').length})()`), 9);
        // Bộ gõ đang soạn thảo và chưa phát compositionend lúc người dùng rời ô.
        assert.equal(await evaluate(`(async()=>{
            const inputs=document.querySelectorAll('form input');
            for(const [index,value] of [[0,'Thử bộ gõ'],[1,'25000'],[2,'4']]){
                const el=inputs[index];el.focus();
                if(index>0) el.dispatchEvent(new CompositionEvent('compositionstart',{bubbles:true}));
                el.value=value;el.dispatchEvent(new InputEvent('input',{bubbles:true,isComposing:index>0}));el.blur();
            }
            document.querySelector('form select').value='Phụ kiện';
            document.querySelector('form select').dispatchEvent(new Event('change',{bubbles:true}));
            await Vue.nextTick();document.querySelector('form button').click();await Vue.nextTick();
            const row=Array.from(document.querySelectorAll('tbody tr')).find(row=>row.cells[0].textContent==='Thử bộ gõ');
            return !!row && row.cells[1].textContent==='25.000 đồng' && row.cells[3].textContent==='4';
        })()`), true, 'Phải thêm được đúng giá và số lượng khi bộ gõ chưa kết thúc soạn thảo');
        // Chuỗi sự kiện lấy từ phiên người dùng: bộ gõ xóa preedit trước pointerdown.
        for (const [name, text, next] of [['ten','Lỗi chuột thực tế','gia'],['gia','17000','ton'],['ton','6','ten']]) {
            await evaluate(`(()=>{
                const el=document.querySelector('form [name="${name}"]');el.focus();
                el.dispatchEvent(new CompositionEvent('compositionstart',{bubbles:true,data:''}));
                el.value=${JSON.stringify(text)};
                el.dispatchEvent(new InputEvent('input',{bubbles:true,inputType:'insertCompositionText',data:${JSON.stringify(text)},isComposing:true}));
                el.value='';
                el.dispatchEvent(new InputEvent('input',{bubbles:true,inputType:'insertCompositionText',data:null,isComposing:true}));
                el.dispatchEvent(new CompositionEvent('compositionend',{bubbles:true,data:''}));
            })()`);
            await click('form [name="'+next+'"]');
        }
        assert.deepEqual(await evaluate(`Array.from(document.querySelectorAll('form input'),el=>el.value)`), ['Lỗi chuột thực tế','17000','6'], 'Phải giữ nội dung khi Fcitx hủy preedit ngay trước cú bấm chuột');
        await click('form button');
        assert.equal(await evaluate(`Array.from(document.querySelectorAll('tbody tr')).some(row=>row.cells[0].textContent==='Lỗi chuột thực tế'&&row.cells[1].textContent==='17.000 đồng'&&row.cells[3].textContent==='6')`), true);
        // Người dùng chủ động xóa/hủy bằng phím thì không được phục hồi nội dung.
        for (const key of ['Backspace', 'Delete', 'Escape']) {
            await evaluate(`(()=>{
                const el=document.querySelector('form [name="ten"]');el.focus();
                el.dispatchEvent(new CompositionEvent('compositionstart',{bubbles:true,data:''}));
                el.value='Cần xóa';el.dispatchEvent(new InputEvent('input',{bubbles:true,isComposing:true,data:'Cần xóa'}));
                el.dispatchEvent(new KeyboardEvent('keydown',{bubbles:true,key:${JSON.stringify(key)}}));
                el.value='';el.dispatchEvent(new InputEvent('input',{bubbles:true,isComposing:true,data:null}));
                el.dispatchEvent(new CompositionEvent('compositionend',{bubbles:true,data:''}));
            })()`);
            await click('form [name="gia"]');
            assert.equal(await evaluate(`document.querySelector('form [name="ten"]').value`), '', key+' phải xóa/hủy được nội dung');
        }
        console.log('PASS: giữ cả ba ô khi cập nhật Vue; bấm chuột chuyển ô và thêm; chuyển ô bằng Tab; bộ gõ hủy preedit trước click; Backspace/Delete/Escape.');
    } finally {
        clearTimeout(deadline);
        if (browser && browser.exitCode === null) {
            const closed = new Promise(resolve => browser.once('exit', resolve));
            browser.kill('SIGKILL'); await closed;
        }
        await fs.rm(dir, { recursive: true, force: true, maxRetries: 3 });
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
