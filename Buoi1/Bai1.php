<?php

$students = [
    ["name" => "Nguyen Van An", "age" => 20, "score" => 8.5],
    ["name" => "Tran Thi Binh", "age" => 21, "score" => 6.5],
    ["name" => "Le Van Cuong", "age" => 19, "score" => 4.5],
    ["name" => "Pham Thi Dung", "age" => 20, "score" => 7.5],
];

foreach ($students as $student) {
    echo "Họ tên: {$student['name']}, Tuổi: {$student['age']}, Điểm: {$student['score']}\n";
}

$total = 0;
foreach ($students as $student) {
    $total += $student['score'];
}
$average = $total / count($students);

echo "\nĐiểm trung bình cả lớp: " . number_format($average, 2) . "\n";