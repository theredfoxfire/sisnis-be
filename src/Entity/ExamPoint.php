<?php

namespace App\Entity;

use App\Repository\ExamPointRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExamPointRepository::class)
 */
class ExamPoint
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $point;

    /**
     * @ORM\ManyToOne(targetEntity=Student::class, inversedBy="examPoints")
     */
    private $student;

    /**
     * @ORM\ManyToOne(targetEntity=Exam::class, inversedBy="examPoints")
     */
    private $exam;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoint(): ?string
    {
        return $this->point;
    }

    public function setPoint(string $point): self
    {
        $this->point = $point;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getExam(): ?Exam
    {
        return $this->exam;
    }

    public function setExam(?Exam $exam): self
    {
        $this->exam = $exam;

        return $this;
    }
}
