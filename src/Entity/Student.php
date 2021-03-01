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

    /**
     * @ORM\OneToMany(targetEntity=ClassHistory::class, mappedBy="student")
     */
    private $classHistories;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDeleted;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $birthDay;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $parentName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $parentAddress;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $religion;

    /**
     * @ORM\OneToMany(targetEntity=StudentAttendance::class, mappedBy="student")
     */
    private $studentAttendances;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="student", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="children", cascade={"persist", "remove"})
     */
    private $parent;

    public function __construct()
    {
        $this->examPoints = new ArrayCollection();
        $this->classHistories = new ArrayCollection();
        $this->studentAttendances = new ArrayCollection();
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

    /**
     * @return Collection|ClassHistory[]
     */
    public function getClassHistories(): Collection
    {
        return $this->classHistories;
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

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBirthDay(): ?string
    {
        return $this->birthDay;
    }

    public function setBirthDay(?string $birthDay): self
    {
        $this->birthDay = $birthDay;

        return $this;
    }

    public function getParentName(): ?string
    {
        return $this->parentName;
    }

    public function setParentName(?string $parentName): self
    {
        $this->parentName = $parentName;

        return $this;
    }

    public function getParentAddress(): ?string
    {
        return $this->parentAddress;
    }

    public function setParentAddress(?string $parentAddress): self
    {
        $this->parentAddress = $parentAddress;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getReligion(): ?string
    {
        return $this->religion;
    }

    public function setReligion(?string $religion): self
    {
        $this->religion = $religion;

        return $this;
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
            $studentAttendance->setStudent($this);
        }

        return $this;
    }

    public function removeStudentAttendance(StudentAttendance $studentAttendance): self
    {
        if ($this->studentAttendances->contains($studentAttendance)) {
            $this->studentAttendances->removeElement($studentAttendance);
            // set the owning side to null (unless already changed)
            if ($studentAttendance->getStudent() === $this) {
                $studentAttendance->setStudent(null);
            }
        }

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

    public function getParent(): ?User
    {
        return $this->parent;
    }

    public function setParent(?User $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
