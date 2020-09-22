<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
  private $manager;

  public function __construct
  (
      ManagerRegistry $registry,
      EntityManagerInterface $manager
  )
  {
      parent::__construct($registry, Student::class);
      $this->manager = $manager;
  }

  public function saveStudent($studentData)
  {
      $student = new Student();

      $student->setSerial($studentData->serial);
      $student->setName($studentData->name);

      $this->manager->persist($student);
      $this->manager->flush();
  }

  public function updateStudent(Student $student, $data)
  {
      empty($data['serial']) ? true : $student->setSerial($data['serial']);
      empty($data['name']) ? true : $student->setName($data['name']);

      $this->manager->flush();
  }

  public function updateStudentClassRoom(Student $student)
  {
      $this->manager->flush();
  }

  public function removeStudent(Student $student)
  {
      $this->manager->remove($student);
      $this->manager->flush();
  }
}
