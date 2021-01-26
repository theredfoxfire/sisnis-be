<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $classRoomName;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $academicYear;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $teacherName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $subjectName;

    /**
     * @ORM\OneToMany(targetEntity=StudentAttendance::class, mappedBy="schedule")
     */
    private $studentAttendances;

    public function __construct()
    {
        $this->studentAttendances = new ArrayCollection();
    }

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

    public function getClassRoomName(): ?string
    {
        return $this->classRoomName;
    }

    public function setClassRoomName(?string $classRoomName): self
    {
        $this->classRoomName = $classRoomName;

        return $this;
    }

    public function getAcademicYear(): ?string
    {
        return $this->academicYear;
    }

    public function setAcademicYear(?string $academicYear): self
    {
        $this->academicYear = $academicYear;

        return $this;
    }

    public function getTeacherName(): ?string
    {
        return $this->teacherName;
    }

    public function setTeacherName(?string $teacherName): self
    {
        $this->teacherName = $teacherName;

        return $this;
    }

    public function getSubjectName(): ?string
    {
        return $this->subjectName;
    }

    public function setSubjectName(?string $subjectName): self
    {
        $this->subjectName = $subjectName;

        return $this;
    }


    public function toArray()
      {
          return [
              'id' => $this->getId(),
              'day' => $this->getDay(),
              'isDeleted' => $this->getIsDeleted(),
              'classRoomName' => $this->getClassRoomName(),
              'academicYear' => $this->getAcademicYear(),
              'teacherName' => $this->getTeacherName(),
              'subjectName' => $this->getSubjectName(),
          ];
      }

    /**
     * @return Collection|StudentAttendance[]
     */
    public function getStudentAttendances(): Collection
    {
        return $this->studentAttendances;
    }

    public function addStudentAttendance(StudentAttendance $studentAttendance): self
    {
        if (!$this->studentAttendances->contains($studentAttendance)) {
            $this->studentAttendances[] = $studentAttendance;
            $studentAttendance->setSchedule($this);
        }

        return $this;
    }

    public function removeStudentAttendance(StudentAttendance $studentAttendance): self
    {
        if ($this->studentAttendances->contains($studentAttendance)) {
            $this->studentAttendances->removeElement($studentAttendance);
            // set the owning side to null (unless already changed)
            if ($studentAttendance->getSchedule() === $this) {
                $studentAttendance->setSchedule(null);
            }
        }

        return $this;
    }
}
