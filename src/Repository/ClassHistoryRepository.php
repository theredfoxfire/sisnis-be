<?php

namespace App\Repository;

use App\Entity\ClassHistory;
use App\Entity\ClassRoom;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method ClassHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClassHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClassHistory[]    findAll()
 * @method ClassHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassHistoryRepository extends ServiceEntityRepository
{
    private $manager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, ClassHistory::class);
        $this->manager = $manager;
    }


    public function saveClassHistory(Student $student, ClassRoom $class)
    {
        $classHistory = new ClassHistory();

        $classHistory->setClass($class);
        $classHistory->setStudent($student);

        $this->manager->persist($classHistory);
        $this->manager->flush();
    }

    // /**
    //  * @return ClassHistory[] Returns an array of ClassHistory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ClassHistory
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
