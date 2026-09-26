<?php

declare(strict_types=1);

class Movie
{
    private int $id;
    private string $title;
    private float $price;
    private int $totalSeats;
    private int $availableSeats;

    public function __construct(int $id, string $title, float $price, int $totalSeats)
    {
        $this->id = $id;
        $this->title = $title;
        $this->price = $price;
        $this->totalSeats = $totalSeats;
        $this->availableSeats = $totalSeats;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function bookTicket(int $quantity): bool
    {
        if ($quantity <= 0) {
            echo "Đặt vé thất bại cho phim '{$this->title}': số lượng vé phải lớn hơn 0.\n";
            return false;
        }

        if ($quantity > $this->availableSeats) {
            echo "Đặt vé thất bại cho phim '{$this->title}': chỉ còn {$this->availableSeats} ghế trống.\n";
            return false;
        }

        $this->availableSeats -= $quantity;
        echo "Đã đặt {$quantity} vé cho phim '{$this->title}'.\n";
        return true;
    }

    public function cancelTicket(int $quantity): bool
    {
        if ($quantity <= 0) {
            echo "Hủy vé thất bại cho phim '{$this->title}': số lượng vé phải lớn hơn 0.\n";
            return false;
        }

        $soldSeats = $this->getSoldSeats();
        if ($quantity > $soldSeats) {
            echo "Hủy vé thất bại cho phim '{$this->title}': chỉ có {$soldSeats} vé đã bán.\n";
            return false;
        }

        $this->availableSeats += $quantity;
        echo "Đã hủy {$quantity} vé của phim '{$this->title}'.\n";
        return true;
    }

    public function getSoldSeats(): int
    {
        return $this->totalSeats - $this->availableSeats;
    }

    public function getRevenue(): float
    {
        return $this->getSoldSeats() * $this->price;
    }

    public function displayInfo(): void
    {
        printf(
            "Mã: %d | Phim: %-10s | Giá vé: %10s VNĐ | Tổng ghế: %3d | Còn trống: %3d | Đã bán: %3d | Doanh thu: %12s VNĐ\n",
            $this->id,
            $this->title,
            number_format($this->price, 0, ',', '.'),
            $this->totalSeats,
            $this->availableSeats,
            $this->getSoldSeats(),
            number_format($this->getRevenue(), 0, ',', '.')
        );
    }
}

function findMovieById(array $movies, int $id): ?Movie
{
    foreach ($movies as $movie) {
        if ($movie->getId() === $id) {
            return $movie;
        }
    }
    return null;
}

function getTotalRevenue(array $movies): float
{
    $total = 0.0;
    foreach ($movies as $movie) {
        $total += $movie->getRevenue();
    }
    return $total;
}

function getBestSellingMovie(array $movies): ?Movie
{
    $best = null;
    foreach ($movies as $movie) {
        if ($best === null || $movie->getSoldSeats() > $best->getSoldSeats()) {
            $best = $movie;
        }
    }
    return $best;
}

echo "1. Tạo danh sách các phim:\n";
$movies = [
    new Movie(1, "Avengers", 100000, 100),
    new Movie(2, "Avatar", 120000, 80),
    new Movie(3, "Batman", 90000, 120),
];
echo "Đã khởi tạo thành công 3 bộ phim.\n";

echo "\n2. Đặt vé cho phim Avengers:\n";
$avengers = findMovieById($movies, 1);
$avengers?->bookTicket(30);

echo "\n3. Đặt vé cho phim Avatar:\n";
$avatar = findMovieById($movies, 2);
$avatar?->bookTicket(50);

echo "\n4. Hủy vé đã đặt của phim Avengers:\n";
$avengers?->cancelTicket(10);

echo "\n5. Hiển thị thông tin tất cả các phim:\n";
foreach ($movies as $movie) {
    $movie->displayInfo();
}

echo "\n6. Tính tổng doanh thu của tất cả các phim:\n";
echo "Tổng doanh thu: " . number_format(getTotalRevenue($movies), 0, ',', '.') . " VNĐ\n";

echo "\n7. Tìm và hiển thị phim có số vé bán ra nhiều nhất:\n";
$best = getBestSellingMovie($movies);
if ($best !== null) {
    echo "Phim bán chạy nhất: {$best->getTitle()} ({$best->getSoldSeats()} vé đã bán).\n";
}

echo "\n8. Kiểm tra đặt vé với số lượng không hợp lệ (<= 0):\n";
$avengers?->bookTicket(0);

echo "\n9. Kiểm tra đặt vé vượt quá số ghế còn lại:\n";
$avengers?->bookTicket(1000);

echo "\n10. Kiểm tra hủy vé với số lượng không hợp lệ (<= 0):\n";
$avengers?->cancelTicket(0);

echo "\n11. Kiểm tra hủy vé vượt quá số vé đã bán:\n";
$avengers?->cancelTicket(1000);

echo "\n12. Kiểm tra tìm phim không tồn tại (ID: 999):\n";
$notFound = findMovieById($movies, 999);
echo "Kết quả tìm kiếm: " . ($notFound === null ? "Không tìm thấy phim." : $notFound->getTitle()) . "\n";

echo "\n13. Kiểm tra các hàm xử lý với danh sách phim rỗng:\n";
$emptyList = [];
echo "Tổng doanh thu (danh sách rỗng): " . number_format(getTotalRevenue($emptyList), 0, ',', '.') . " VNĐ\n";
$bestEmpty = getBestSellingMovie($emptyList);
echo "Phim bán chạy nhất (danh sách rỗng): " . ($bestEmpty === null ? "Không có phim nào." : $bestEmpty->getTitle()) . "\n";