<?php

$name = "Nguyễn Việt Anh";
$score = 8.5;

if ($score >= 8) {
    $rank = "Giỏi";
} elseif ($score >= 6.5) {
    $rank = "Khá";
} elseif ($score >= 5) {
    $rank = "Trung bình";
} else {
    $rank = "Yếu";
}

echo "Họ và tên: " . $name . "<br>\n";
echo "Điểm số: " . $score . "<br>\n";
echo "Xếp loại: " . $rank . "<br>\n";
?>