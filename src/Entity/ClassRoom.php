<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClassRoomRepository")
 */
class ClassRoom
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Student::class, mappedBy="classRoom")
     */
    private $students;

    /**
     * @ORM\OneToMany(targetEntity=TeacherClassToSubject::class, mappedBy="classRoom")
     */
    private $teacherClassToSubjects;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->teacherClassToSubjects = new ArrayCollection();
    }

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

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }

    /**
     * @return Collection|Student[]
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->setClassRoom($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->contains($student)) {
            $this->students->removeElement($student);
            // set the owning side to null (unless already changed)
            if ($student->getClassRoom() === $this) {
                $student->setClassRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TeacherClassToSubject[]
     */
    public function getTeacherClassToSubjects(): Collection
    {
        return $this->teacherClassToSubjects;
    }

    public function addTeacherClassToSubject(TeacherClassToSubject $teacherClassToSubject): self
    {
        if (!$this->teacherClassToSubjects->contains($teacherClassToSubject)) {
            $this->teacherClassToSubjects[] = $teacherClassToSubject;
            $teacherClassToSubject->setClassRoom($this);
        }

        return $this;
    }

    public function removeTeacherClassToSubject(TeacherClassToSubject $teacherClassToSubject): self
    {
        if ($this->teacherClassToSubjects->contains($teacherClassToSubject)) {
            $this->teacherClassToSubjects->removeElement($teacherClassToSubject);
            // set the owning side to null (unless already changed)
            if ($teacherClassToSubject->getClassRoom() === $this) {
                $teacherClassToSubject->setClassRoom(null);
            }
        }

        return $this;
    }
}
