<?php

$students = [
    ["name" => "Nguyen Van An", "age" => 20, "score" => 8.5],
    ["name" => "Tran Thi Binh", "age" => 21, "score" => 6.5],
    ["name" => "Le Van Cuong", "age" => 19, "score" => 4.5],
    ["name" => "Pham Thi Dung", "age" => 20, "score" => 7.5],
];

function calculateAverageScore(array $students): float
{
    if (count($students) === 0) {
        return 0;
    }

    $total = 0;
    foreach ($students as $student) {
        $total += $student['score'];
    }

    return $total / count($students);
}

function getRank(float $score): string
{
    if ($score >= 8) {
        return "Giỏi";
    } elseif ($score >= 6.5) {
        return "Khá";
    } elseif ($score >= 5) {
        return "Trung bình";
    }

    return "Yếu";
}

function displayStudent(array $student): void
{
    $rank = getRank($student['score']);
    echo "Họ tên: {$student['name']}, Tuổi: {$student['age']}, Điểm: {$student['score']}, Xếp loại: {$rank}\n";
}

foreach ($students as $student) {
    displayStudent($student);
}

$average = calculateAverageScore($students);
echo "\nĐiểm trung bình cả lớp: " . number_format($average, 2) . "\n";