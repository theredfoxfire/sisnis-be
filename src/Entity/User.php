<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User implements UserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=45, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDeleted;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $passportAccess;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $passportExpiry;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPassportActive;

    /**
     * @ORM\OneToOne(targetEntity=Student::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $student;

    /**
     * @ORM\OneToOne(targetEntity=Student::class, mappedBy="parent", cascade={"persist", "remove"})
     */
    private $children;

    /**
     * @ORM\OneToOne(targetEntity=Teacher::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $teacher;

    /**
     * User constructor.
     * @param $username
     */
    public function __construct($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function getRoles(): Array {
        return json_decode($this->roles);
    }

    public function eraseCredentials()
    {
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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function setRoles(?string $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassportAccess(): ?string
    {
        return $this->passportAccess;
    }

    public function setPassportAccess(?string $passportAccess): self
    {
        $this->passportAccess = $passportAccess;

        return $this;
    }

    public function getPassportExpiry(): ?string
    {
        return $this->passportExpiry;
    }

    public function setPassportExpiry(?string $passportExpiry): self
    {
        $this->passportExpiry = $passportExpiry;

        return $this;
    }

    public function getIsPassportActive(): ?bool
    {
        return $this->isPassportActive;
    }

    public function setIsPassportActive(?bool $isPassportActive): self
    {
        $this->isPassportActive = $isPassportActive;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        // set (or unset) the owning side of the relation if necessary
        $newUserId = null === $student ? null : $this;
        if ($student->getUserId() !== $newUserId) {
            $student->setUserId($newUserId);
        }

        return $this;
    }

    public function getChildren(): ?Student
    {
        return $this->children;
    }

    public function setChildren(?Student $children): self
    {
        $this->children = $children;

        // set (or unset) the owning side of the relation if necessary
        $newParent = null === $children ? null : $this;
        if ($children->getParent() !== $newParent) {
            $children->setParent($newParent);
        }

        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        // set (or unset) the owning side of the relation if necessary
        $newUserId = null === $teacher ? null : $this;
        if ($teacher->getUserId() !== $newUserId) {
            $teacher->setUserId($newUserId);
        }

        return $this;
    }
}
