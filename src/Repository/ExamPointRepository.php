<?php

namespace App\Repository;

use App\Entity\ExamPoint;
use App\Entity\Exam;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method ExamPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExamPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExamPoint[]    findAll()
 * @method ExamPoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamPointRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct
    (
        ManagerRegistry $registry,
        EntityManagerInterface $manager
    )
    {
        parent::__construct($registry, ExamPoint::class);
        $this->manager = $manager;
    }

    public function saveExamPoint($point, Exam $exam, Student $student)
    {
        $examPoint = new ExamPoint();

        $examPoint->setExam($exam);
        $examPoint->setStudent($student);
        $examPoint->setPoint($point);

        $this->manager->persist($examPoint);
        $this->manager->flush();
    }

    // /**
    //  * @return ExamPoint[] Returns an array of ExamPoint objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExamPoint
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
