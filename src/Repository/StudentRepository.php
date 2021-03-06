<?php

namespace App\Repository;

use App\Entity\Student;
use App\Entity\User;
use App\Entity\ClassRoom;
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
  private $classStatus;

  public function __construct
  (
      ManagerRegistry $registry,
      EntityManagerInterface $manager
  )
  {
      parent::__construct($registry, Student::class);
      $this->manager = $manager;
      $this->classStatus = (object)['yes' => 'YES', 'no' => 'NO'];
  }

  public function saveStudent($studentData, ClassRoom $classRoom, User $userStudent, User $userParent)
  {
      $student = new Student();

      $student->setSerial($studentData->serial);
      $student->setName($studentData->name);
      $student->setGender($studentData->gender);
      $student->setBirthDay($studentData->birthDay);
      $student->setParentName($studentData->parentName);
      $student->setParentAddress($studentData->parentAddress);
      $student->setCity($studentData->city);
      $student->setUserId($userStudent);
      $student->setParent($userParent);
      $student->setReligion($studentData->religion);
      if ($classRoom) {
        $student->setClassRoom($classRoom);
      }

      $this->manager->persist($student);
      $this->manager->flush();
  }

  public function updateStudent(Student $student, $studentData)
  {
      empty($data->serial) ? true : $student->setSerial($studentData->serial);
      empty($data->name) ? true : $student->setName($studentData->name);
      empty($data->gender) ? true : $student->setGender($studentData->gender);
      empty($data->birthDay) ? true : $student->setBirthDay($studentData->birthDay);
      empty($data->parentName) ? true : $student->setParentName($studentData->parentName);
      empty($data->parentAddress) ? true : $student->setParentAddress($studentData->parentAddress);
      empty($data->city) ? true : $student->setCity($studentData->city);
      empty($data->religion) ? true : $student->setReligion($studentData->religion);

      $this->manager->flush();
  }

  public function updateStudentClassRoom(Student $student)
  {
      $this->manager->flush();
  }

  public function removeStudent(Student $student)
  {
    $student->setIsDeleted(true);

    $this->manager->flush();
  }

  /**
   * @return ExamPoint[] Returns an array of ExamPoint objects
   *
   * ->andWhere('e.exampleField = :val')
   * ->setParameter('val', $value)
   */
  public function getAllStudents($start = 0, $max = 25, $name="", $haveClass="")
  {
      $query = $this->createQueryBuilder('e');
      if (strtoupper($haveClass) == $this->classStatus->no) {
        $query->where('e.classRoom IS NULL');
      }
      if (strtoupper($haveClass) == $this->classStatus->yes) {
        $query->where('e.classRoom IS NOT NULL');
      }
      if ($name != "") {
        $query->andWhere('e.name LIKE :name')->setParameter('name', '%'.$name.'%');
      }
      $query->andWhere('e.isDeleted IS NULL');
      $query->orWhere('e.isDeleted = false');
      $data = $query->orderBy('e.id', 'ASC')
          ->setFirstResult($start)
          ->setMaxResults($max)
          ->getQuery()->getResult();

      $totals = $this->createQueryBuilder('e')
      ->where('e.isDeleted IS NULL')
      ->orWhere('e.isDeleted = false')
          ->getQuery()
          ->getResult();
      return (object) array('totals' => count($totals), 'data' => $data);
  }
}
