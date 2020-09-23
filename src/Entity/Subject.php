<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubjectRepository")
 */
class Subject
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
     * @ORM\OneToMany(targetEntity=TeacherClassToSubject::class, mappedBy="subject")
     */
    private $teacherClassToSubjects;

    public function __construct()
    {
        $this->teachers = new ArrayCollection();
        $this->teacherClassToSubjects = new ArrayCollection();
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
            'name' => $this->getName(),
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
            $teacherClassToSubject->setSubject($this);
        }

        return $this;
    }

    public function removeTeacherClassToSubject(TeacherClassToSubject $teacherClassToSubject): self
    {
        if ($this->teacherClassToSubjects->contains($teacherClassToSubject)) {
            $this->teacherClassToSubjects->removeElement($teacherClassToSubject);
            // set the owning side to null (unless already changed)
            if ($teacherClassToSubject->getSubject() === $this) {
                $teacherClassToSubject->setSubject(null);
            }
        }

        return $this;
    }
}
