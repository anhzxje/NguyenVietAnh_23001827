<?php

class Student
{
    private string $name;
    private int $age;
    private float $score;

    public function __construct(string $name, int $age, float $score)
    {
        $this->name = $name;
        $this->age = $age;
        $this->score = $score;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function getRank(): string
    {
        if ($this->score >= 8) {
            return "Giỏi";
        } elseif ($this->score >= 6.5) {
            return "Khá";
        } elseif ($this->score >= 5) {
            return "Trung bình";
        }

        return "Yếu";
    }

    public function isPassed(): bool
    {
        return $this->score >= 5;
    }

    public function display(): void
    {
        echo "Họ tên: {$this->name}, Tuổi: {$this->age}, Điểm: {$this->score}, Xếp loại: {$this->getRank()}\n";
    }
}

class StudentList
{
    /** @var Student[] */
    private array $students = [];

    public function add(Student $student): void
    {
        $this->students[] = $student;
    }

    public function displayAll(): void
    {
        foreach ($this->students as $student) {
            $student->display();
        }
    }

    public function findBestStudent(): ?Student
    {
        if (count($this->students) === 0) {
            return null;
        }

        $best = $this->students[0];
        foreach ($this->students as $student) {
            if ($student->getScore() > $best->getScore()) {
                $best = $student;
            }
        }

        return $best;
    }

    public function countPassedStudents(): int
    {
        $count = 0;
        foreach ($this->students as $student) {
            if ($student->isPassed()) {
                $count++;
            }
        }

        return $count;
    }

    public function calculateAverageScore(): float
    {
        if (count($this->students) === 0) {
            return 0;
        }

        $total = 0;
        foreach ($this->students as $student) {
            $total += $student->getScore();
        }

        return $total / count($this->students);
    }
}

// ==== Chương trình chính ====
$student1 = new Student("Nguyen Van An", 20, 8.5);
$student2 = new Student("Tran Thi Binh", 21, 6.5);
$student3 = new Student("Le Van Cuong", 19, 4.5);
$student4 = new Student("Pham Thi Dung", 20, 7.5);

$classList = new StudentList();
$classList->add($student1);
$classList->add($student2);
$classList->add($student3);
$classList->add($student4);

echo "Danh sách sinh viên:\n";
$classList->displayAll();

echo "\nSinh viên điểm cao nhất:\n";
$classList->findBestStudent()?->display();

echo "\nSố sinh viên đạt: " . $classList->countPassedStudents() . "\n";
echo "\nĐiểm trung bình cả lớp: " . number_format($classList->calculateAverageScore(), 2) . "\n";