<?php

namespace App\Entity;

use App\Repository\AcademicYearRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AcademicYearRepository::class)
 */
class AcademicYear
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $year;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity=TeacherClassToSubject::class, mappedBy="academicYear")
     */
    private $subject;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDeleted;

    public function __construct()
    {
        $this->subject = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function toArray()
    {
        return [
            'yearId' => $this->getId(),
            'year' => $this->getYear(),
            'isActive' => $this->getIsActive() ? "TRUE" : "FALSE",
        ];
    }

    /**
     * @return Collection|TeacherClassToSubject[]
     */
    public function getSubject(): Collection
    {
        return $this->subject;
    }

    public function addSubject(TeacherClassToSubject $subject): self
    {
        if (!$this->subject->contains($subject)) {
            $this->subject[] = $subject;
            $subject->setAcademicYear($this);
        }

        return $this;
    }

    public function removeSubject(TeacherClassToSubject $subject): self
    {
        if ($this->subject->contains($subject)) {
            $this->subject->removeElement($subject);
            // set the owning side to null (unless already changed)
            if ($subject->getAcademicYear() === $this) {
                $subject->setAcademicYear(null);
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
}
