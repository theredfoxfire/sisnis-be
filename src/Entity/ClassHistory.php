<?php

namespace App\Entity;

use App\Repository\ClassHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClassHistoryRepository::class)
 */
class ClassHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ClassRoom::class, inversedBy="classHistories")
     */
    private $class;

    /**
     * @ORM\ManyToOne(targetEntity=Student::class, inversedBy="classHistories")
     */
    private $student;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDeleted;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClass(): ?ClassRoom
    {
        return $this->class;
    }

    public function setClass(?ClassRoom $class): self
    {
        $this->class = $class;

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

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }
}
