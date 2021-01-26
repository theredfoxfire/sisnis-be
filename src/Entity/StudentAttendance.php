<?php

namespace App\Entity;

use App\Repository\StudentAttendanceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StudentAttendanceRepository::class)
 */
class StudentAttendance
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Schedule::class, inversedBy="studentAttendances")
     */
    private $schedule;

    /**
     * @ORM\ManyToOne(targetEntity=Student::class, inversedBy="studentAttendances")
     */
    private $student;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $presenceStatus;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notes;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDeleted;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(?Schedule $schedule): self
    {
        $this->schedule = $schedule;

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

    public function getPresenceStatus(): ?string
    {
        return $this->presenceStatus;
    }

    public function setPresenceStatus(?string $presenceStatus): self
    {
        $this->presenceStatus = $presenceStatus;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

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
