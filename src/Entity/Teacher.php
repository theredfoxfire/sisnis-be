<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeacherRepository")
 */
class Teacher
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $serial;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=TeacherClassToSubject::class, mappedBy="teacher")
     */
    private $teacherClassToSubjects;

    /**
     * @ORM\OneToMany(targetEntity=ClassRoom::class, mappedBy="guardian")
     */
    private $guardianClass;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDeleted;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="teacher", cascade={"persist", "remove"})
     */
    private $user;

    public function __construct()
    {
        $this->teacherClassToSubjects = new ArrayCollection();
        $this->guardianClass = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerial(): ?string
    {
        return $this->serial;
    }

    public function setSerial(string $serial): self
    {
        $this->serial = $serial;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'serial' => $this->getSerial(),
            'name' => ucfirst($this->getName()),
        ];
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
            $teacherClassToSubject->setTeacher($this);
        }

        return $this;
    }

    public function removeTeacherClassToSubject(TeacherClassToSubject $teacherClassToSubject): self
    {
        if ($this->teacherClassToSubjects->contains($teacherClassToSubject)) {
            $this->teacherClassToSubjects->removeElement($teacherClassToSubject);
            // set the owning side to null (unless already changed)
            if ($teacherClassToSubject->getTeacher() === $this) {
                $teacherClassToSubject->setTeacher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ClassRoom[]
     */
    public function getGuardianClass(): Collection
    {
        return $this->guardianClass;
    }

    public function addGuardianClass(ClassRoom $guardianClass): self
    {
        if (!$this->guardianClass->contains($guardianClass)) {
            $this->guardianClass[] = $guardianClass;
            $guardianClass->setGuardian($this);
        }

        return $this;
    }

    public function removeGuardianClass(ClassRoom $guardianClass): self
    {
        if ($this->guardianClass->contains($guardianClass)) {
            $this->guardianClass->removeElement($guardianClass);
            // set the owning side to null (unless already changed)
            if ($guardianClass->getGuardian() === $this) {
                $guardianClass->setGuardian(null);
            }
        }

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

    public function getUserId(): ?User
    {
        return $this->user;
    }

    public function setUserId(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
