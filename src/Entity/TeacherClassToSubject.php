<?php

namespace App\Entity;

use App\Repository\TeacherClassToSubjectRepository;
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
}
