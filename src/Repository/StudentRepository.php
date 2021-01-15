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
      if ($name != "" && $haveClass != "") {
        $query->andWhere('e.name LIKE :name')->setParameter('name', $name.'%');
      }
      if ($name != "" && $haveClass == "") {
        $query->where('e.name LIKE :name')->setParameter('name', $name.'%');
      }
      $query->orWhere('e.isDeleted IS NULL');
      $query->orWhere('e.isDeleted = false');
      $data = $query->orderBy('e.id', 'ASC')
          ->setFirstResult($start)
          ->setMaxResults($max)
          ->getQuery()->getResult();

      $totals = $this->createQueryBuilder('e')
          ->getQuery()
          ->getResult();
      return (object) array('totals' => count($totals), 'data' => $data);
  }
//TODO: add search action
// $repository = $em->getRepository('AcmeCrawlerBundle:Trainings');
//        $query = $repository->createQueryBuilder('p')
//                ->where('p.title LIKE :word')
//                ->orWhere('p.discription LIKE :word')
//                ->setParameter('word', '%'.$word.'%')
//                ->getQuery();
//         $trainings = $query->getResult();
}
