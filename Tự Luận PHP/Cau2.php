<?php
$prices = [18, 29, 30, 36, 38];

function calculateTotal($prices) {
    $total = 0;
    foreach ($prices as $price) {
        $total += $price;
    }
    return $total;
}

function findMaxPrice($prices) {
    $max = $prices[0];
    foreach ($prices as $price) {
        if ($price > $max) {
            $max = $price;
        }
    }
    return $max;
}

$total = calculateTotal($prices);
$max = findMaxPrice($prices);

echo "Danh sách giá sản phẩm: " . implode(", ", $prices) . "<br>\n";
echo "Tổng giá trị của tất cả sản phẩm: " . $total . "<br>\n";
echo "Giá sản phẩm cao nhất: " . $max . "<br>\n";
?>
