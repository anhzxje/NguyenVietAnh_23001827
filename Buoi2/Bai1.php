<?php

declare(strict_types=1);

class CartItem
{
    private string $name;
    private float $price;
    private int $quantity;

    public function __construct(string $name, float $price, int $quantity)
    {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTotal(): float
    {
        return $this->price * $this->quantity;
    }
}

class ShoppingCart
{
    private array $items = [];

    public function addItem(CartItem $item): void
    {
        if ($item->getPrice() <= 0) {
            echo "Không thể thêm '{$item->getName()}': đơn giá phải lớn hơn 0.\n";
            return;
        }

        if ($item->getQuantity() <= 0) {
            echo "Không thể thêm '{$item->getName()}': số lượng phải lớn hơn 0.\n";
            return;
        }

        $this->items[] = $item;
    }

    public function removeItem(string $name): void
    {
        foreach ($this->items as $index => $item) {
            if ($item->getName() === $name) {
                unset($this->items[$index]);
                $this->items = array_values($this->items);
                echo "Đã xóa sản phẩm '{$name}' khỏi giỏ hàng.\n";
                return;
            }
        }

        echo "Không tìm thấy sản phẩm '{$name}' trong giỏ hàng.\n";
    }

    public function calculateTotal(): float
    {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item->getTotal();
        }
        return $total;
    }

    private function utf8Length(string $str): int
    {
        return function_exists('mb_strlen')
            ? mb_strlen($str, 'UTF-8')
            : count(preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY));
    }

    public function displayCart(): void
    {
        echo "[ GIỎ HÀNG ]\n";

        if (empty($this->items)) {
            echo "Giỏ hàng đang trống.\n";
            return;
        }

        foreach ($this->items as $item) {
            $namePadded = $item->getName() . str_repeat(' ', max(0, 15 - $this->utf8Length($item->getName())));
            printf(
                "- %s | Đơn giá: %10s VNĐ | SL: %2d | Thành tiền: %12s VNĐ\n",
                $namePadded,
                number_format($item->getPrice(), 0, ',', '.'),
                $item->getQuantity(),
                number_format($item->getTotal(), 0, ',', '.')
            );
        }

        echo "Tổng tiền: " . number_format($this->calculateTotal(), 0, ',', '.') . " VNĐ\n";
    }
}

$cart = new ShoppingCart();

$item1 = new CartItem("Áo thun", 150000, 2);
$item2 = new CartItem("Quần jean", 350000, 1);
$item3 = new CartItem("Giày thể thao", 500000, 1);
$item4 = new CartItem("Nón lưỡi trai", 80000, 3);

$cart->addItem($item1);
$cart->addItem($item2);
$cart->addItem($item3);
$cart->addItem($item4);

echo "1. Hiển thị toàn bộ giỏ hàng ban đầu:\n";
$cart->displayCart();

echo "\n2. Tính và hiển thị tổng tiền của giỏ hàng:\n";
echo "Tổng tiền giỏ hàng: " . number_format($cart->calculateTotal(), 0, ',', '.') . " VNĐ\n";

echo "\n3. Xóa sản phẩm 'Quần jean' khỏi giỏ hàng:\n";
$cart->removeItem("Quần jean");

echo "\n4. Hiển thị lại giỏ hàng sau khi xóa:\n";
$cart->displayCart();

echo "\n5. Kiểm tra trường hợp thêm sản phẩm có đơn giá không hợp lệ (<= 0):\n";
$cart->addItem(new CartItem("Sản phẩm giá âm", -10000, 1));

echo "\n6. Kiểm tra trường hợp thêm sản phẩm có số lượng không hợp lệ (<= 0):\n";
$cart->addItem(new CartItem("Sản phẩm số lượng 0", 50000, 0));

echo "\n7. Kiểm tra trường hợp xóa sản phẩm không tồn tại trong giỏ hàng:\n";
$cart->removeItem("Điện thoại");

echo "\n8. Kiểm tra trường hợp giỏ hàng rỗng:\n";
$emptyCart = new ShoppingCart();
echo "Tổng tiền giỏ hàng rỗng: " . number_format($emptyCart->calculateTotal(), 0, ',', '.') . " VNĐ\n";
$emptyCart->displayCart();