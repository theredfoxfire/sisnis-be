<?php

namespace App\Entity;

use App\Repository\ExamRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExamRepository::class)
 */
class Exam
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=TeacherClassToSubject::class, inversedBy="exams")
     */
    private $teacherSubject;

    /**
     * @ORM\ManyToOne(targetEntity=ExamType::class, inversedBy="exams")
     */
    private $examType;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTeacherSubject(): ?TeacherClassToSubject
    {
        return $this->teacherSubject;
    }

    public function setTeacherSubject(?TeacherClassToSubject $teacherSubject): self
    {
        $this->teacherSubject = $teacherSubject;

        return $this;
    }
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'date' => $this->getDate(),
        ];
    }

    public function getExamType(): ?ExamType
    {
        return $this->examType;
    }

    public function setExamType(?ExamType $examType): self
    {
        $this->examType = $examType;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }
}
