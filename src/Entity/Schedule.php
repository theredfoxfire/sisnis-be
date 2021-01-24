<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ScheduleRepository::class)
 */
class Schedule
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TimeSlot::class, inversedBy="schedules")
     */
    private $timeSlot;

    /**
     * @ORM\ManyToOne(targetEntity=Room::class, inversedBy="schedules")
     */
    private $room;

    /**
     * @ORM\ManyToOne(targetEntity=TeacherClassToSubject::class, inversedBy="schedules")
     */
    private $subject;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $day;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDeleted;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeSlot(): ?TimeSlot
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(?TimeSlot $timeSlot): self
    {
        $this->timeSlot = $timeSlot;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getSubject(): ?TeacherClassToSubject
    {
        return $this->subject;
    }

    public function setSubject(?TeacherClassToSubject $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(?string $day): self
    {
        $this->day = $day;

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
