<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentRepository")
 */
class Student
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=ClassRoom::class, inversedBy="students")
     */
    private $classRoom;

    /**
     * @ORM\OneToMany(targetEntity=ExamPoint::class, mappedBy="student")
     */
    private $examPoints;

    public function __construct()
    {
        $this->examPoints = new ArrayCollection();
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

    public function getClassRoom(): ?ClassRoom
    {
        return $this->classRoom;
    }

    public function setClassRoom(?ClassRoom $classRoom): self
    {
        $this->classRoom = $classRoom;

        return $this;
    }

    /**
     * @return Collection|ExamPoint[]
     */
    public function getExamPoints(): Collection
    {
        return $this->examPoints;
    }

    public function addExamPoint(ExamPoint $examPoint): self
    {
        if (!$this->examPoints->contains($examPoint)) {
            $this->examPoints[] = $examPoint;
            $examPoint->setStudent($this);
        }

        return $this;
    }

    public function removeExamPoint(ExamPoint $examPoint): self
    {
        if ($this->examPoints->contains($examPoint)) {
            $this->examPoints->removeElement($examPoint);
            // set the owning side to null (unless already changed)
            if ($examPoint->getStudent() === $this) {
                $examPoint->setStudent(null);
            }
        }

        return $this;
    }
}
