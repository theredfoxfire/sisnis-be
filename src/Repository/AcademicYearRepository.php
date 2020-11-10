<?php

namespace App\Repository;

use App\Entity\AcademicYear;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method AcademicYear|null find($id, $lockMode = null, $lockVersion = null)
 * @method AcademicYear|null findOneBy(array $criteria, array $orderBy = null)
 * @method AcademicYear[]    findAll()
 * @method AcademicYear[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AcademicYearRepository extends ServiceEntityRepository
{
  private $manager;
  private $classStatus;

  public function __construct
  (
      ManagerRegistry $registry,
      EntityManagerInterface $manager
  )
  {
      parent::__construct($registry, AcademicYear::class);
      $this->manager = $manager;
      $this->classStatus = (object)['yes' => 'YES', 'no' => 'NO'];
  }

  public function saveAcademicYear($academicYearData)
  {
      $academicYear = new AcademicYear();

      $academicYear->setYear($academicYearData->year);
      $academicYear->setIsActive($academicYearData->isActive === "TRUE" ? true : false);

      $this->manager->persist($academicYear);
      $this->manager->flush();
  }

  public function updateAcademicYear(AcademicYear $academicYear, $data)
  {
      empty($data['year']) ? true : $academicYear->setYear($data['year']);
      empty($data['isActive']) ? true : $academicYear->setIsActive($data['isActive'] === "TRUE" ? true : false);

      $this->manager->flush();
  }

  public function updateAcademicYearClassRoom(AcademicYear $academicYear)
  {
      $this->manager->flush();
  }

  public function removeAcademicYear(AcademicYear $academicYear)
  {
      $this->manager->remove($academicYear);
      $this->manager->flush();
  }

  /**
   * @return ExamPoint[] Returns an array of ExamPoint objects
   *
   * ->andWhere('e.exampleField = :val')
   * ->setParameter('val', $value)
   */
  public function getAllAcademicYears($start = 0, $max = 25, $name="", $haveClass="")
  {
      $query = $this->createQueryBuilder('e');
      // if (strtoupper($haveClass) == $this->classStatus->no) {
      //   $query->where('e.classRoom IS NULL');
      // }
      // if (strtoupper($haveClass) == $this->classStatus->yes) {
      //   $query->where('e.classRoom IS NOT NULL');
      // }
      // if ($name != "" && $haveClass != "") {
      //   $query->andWhere('e.name LIKE :name')->setParameter('name', $name.'%');
      // }
      // if ($name != "" && $haveClass == "") {
      //   $query->where('e.name LIKE :name')->setParameter('name', $name.'%');
      // }
      $data = $query->orderBy('e.id', 'ASC')
          ->setFirstResult($start)
          ->setMaxResults($max)
          ->getQuery()->getResult();

      $totals = $this->createQueryBuilder('e')
          ->getQuery()
          ->getResult();
      return (object) array('totals' => count($totals), 'data' => $data);
  }
}
