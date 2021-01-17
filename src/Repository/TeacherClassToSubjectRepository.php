<?php

namespace App\Repository;

use App\Entity\TeacherClassToSubject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method TeacherClassToSubject|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeacherClassToSubject|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeacherClassToSubject[]    findAll()
 * @method TeacherClassToSubject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeacherClassToSubjectRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct
    (
      ManagerRegistry $registry,
      EntityManagerInterface $manager
    )
    {
      parent::__construct($registry, TeacherClassToSubject::class);
      $this->manager = $manager;
    }

    public function updateTeacherClassRoom(TeacherClassToSubject $teacherMap)
    {
        $this->manager->persist($teacherMap);
        $this->manager->flush();
    }

    public function removeTeacherMap(TeacherClassToSubject $teacherMap)
    {
      $teacherMap->setIsDeleted(true);

      $this->manager->flush();
    }

    // /**
    //  * @return TeacherClassToSubject[] Returns an array of TeacherClassToSubject objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TeacherClassToSubject
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
