<?php

namespace App\Repository;

use App\Entity\ExamType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method ExamType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExamType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExamType[]    findAll()
 * @method ExamType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamTypeRepository extends ServiceEntityRepository
{
  private $manager;

  public function __construct
  (
      ManagerRegistry $registry,
      EntityManagerInterface $manager
  )
  {
      parent::__construct($registry, ExamType::class);
      $this->manager = $manager;
  }

  public function getAllExamType() {
      $query = $this->createQueryBuilder('e');
      $query->where('e.isDeleted IS NULL');
      $query->orWhere('e.isDeleted = false');
      $data = $query->orderBy('e.id', 'ASC')
          ->getQuery()->getResult();
      return (object) $data;
    }

  public function saveExamType($examTypeData)
  {
      $examType = new ExamType();

      $examType->setScale($examTypeData->scale);
      $examType->setName($examTypeData->name);

      $this->manager->persist($examType);
      $this->manager->flush();
  }

  public function updateExamType(ExamType $examType, $data)
  {
      empty($data['scale']) ? true : $examType->setScale($data['scale']);
      empty($data['name']) ? true : $examType->setName($data['name']);

      $this->manager->flush();
  }

  public function removeExamType(ExamType $examType)
  {
    $examType->setIsDeleted(true);

    $this->manager->flush();
  }
}
