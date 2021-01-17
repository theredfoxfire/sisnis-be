<?php

namespace App\Repository;

use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Subject|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subject|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subject[]    findAll()
 * @method Subject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubjectRepository extends ServiceEntityRepository
{
  private $manager;

  public function __construct
  (
      ManagerRegistry $registry,
      EntityManagerInterface $manager
  )
  {
      parent::__construct($registry, Subject::class);
      $this->manager = $manager;
  }

  public function getAllSubject() {
      $query = $this->createQueryBuilder('e');
      $query->where('e.isDeleted IS NULL');
      $query->orWhere('e.isDeleted = false');
      $data = $query->orderBy('e.id', 'ASC')
          ->getQuery()->getResult();
      return (object) $data;
    }

  public function saveSubject($subjectData)
  {
      $subject = new Subject();

      $subject->setSerial($subjectData->serial);
      $subject->setName($subjectData->name);

      $this->manager->persist($subject);
      $this->manager->flush();
  }

  public function updateSubject(Subject $subject, $data)
  {
      empty($data['serial']) ? true : $subject->setSerial($data['serial']);
      empty($data['name']) ? true : $subject->setName($data['name']);

      $this->manager->flush();
  }

  public function removeSubject(Subject $subject)
  {
    $subject->setIsDeleted(true);

    $this->manager->flush();
  }
}
