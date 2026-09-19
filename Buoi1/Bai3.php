<?php

$students = [
    ["name" => "Nguyen Van An", "age" => 20, "score" => 8.5],
    ["name" => "Tran Thi Binh", "age" => 21, "score" => 6.5],
    ["name" => "Le Van Cuong", "age" => 19, "score" => 4.5],
    ["name" => "Pham Thi Dung", "age" => 20, "score" => 7.5],
];

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

function findBestStudent(array $students): ?array
{
    if (count($students) === 0) {
        return null;
    }

    $best = $students[0];
    foreach ($students as $student) {
        if ($student['score'] > $best['score']) {
            $best = $student;
        }
    }

    return $best;
}

function findWorstStudent(array $students): ?array
{
    if (count($students) === 0) {
        return null;
    }

    $worst = $students[0];
    foreach ($students as $student) {
        if ($student['score'] < $worst['score']) {
            $worst = $student;
        }
    }

    return $worst;
}

function countPassedStudents(array $students): int
{
    $count = 0;
    foreach ($students as $student) {
        if ($student['score'] >= 5) {
            $count++;
        }
    }

    return $count;
}

function findStudentByName(array $students, string $name): ?array
{
    foreach ($students as $student) {
        if (strcasecmp($student['name'], $name) === 0) {
            return $student;
        }
    }

    return null;
}

echo "Sinh viên có điểm cao nhất: \n";
displayStudent(findBestStudent($students));

echo "\nSinh viên có điểm thấp nhất: \n";
displayStudent(findWorstStudent($students));

echo "\nSố sinh viên đạt: " . countPassedStudents($students) . "\n";

$searchName = "Le Van Cuong";
$found = findStudentByName($students, $searchName);

echo "\nTìm sinh viên tên \"$searchName\":\n";
if ($found !== null) {
    displayStudent($found);
} else {
    echo "Không tìm thấy sinh viên này.\n";
}