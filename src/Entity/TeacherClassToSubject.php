<?php

namespace App\Entity;

use App\Repository\TeacherClassToSubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TeacherClassToSubjectRepository::class)
 */
class TeacherClassToSubject
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Teacher::class, inversedBy="teacherClassToSubjects")
     */
    private $teacher;

    /**
     * @ORM\ManyToOne(targetEntity=ClassRoom::class, inversedBy="teacherClassToSubjects")
     */
    private $classRoom;

    /**
     * @ORM\ManyToOne(targetEntity=Subject::class, inversedBy="teacherClassToSubjects")
     */
    private $subject;

    /**
     * @ORM\OneToMany(targetEntity=Exam::class, mappedBy="teacherSubject")
     */
    private $exams;

    /**
     * @ORM\ManyToOne(targetEntity=AcademicYear::class, inversedBy="subject")
     */
    private $academicYear;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDeleted;

    public function __construct()
    {
        $this->exams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getClassRoom(): ?ClassRoom
    {
        return $this->classRoom;
    }

    public function setClassRoom(?ClassRoom $classRoom): self
    {
        $this->classRoom = $classRoom;

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return Collection|Exam[]
     */
    public function getExams(): Collection
    {
        return $this->exams;
    }

    public function addExam(Exam $exam): self
    {
        if (!$this->exams->contains($exam)) {
            $this->exams[] = $exam;
            $exam->setTeacherSubject($this);
        }

        return $this;
    }

    public function removeExam(Exam $exam): self
    {
        if ($this->exams->contains($exam)) {
            $this->exams->removeElement($exam);
            // set the owning side to null (unless already changed)
            if ($exam->getTeacherSubject() === $this) {
                $exam->setTeacherSubject(null);
            }
        }

        return $this;
    }

    public function getAcademicYear(): ?AcademicYear
    {
        return $this->academicYear;
    }

    public function setAcademicYear(?AcademicYear $academicYear): self
    {
        $this->academicYear = $academicYear;

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
