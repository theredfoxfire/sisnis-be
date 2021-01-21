<?php

namespace App\Repository;

use App\Entity\TimeSlot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method TimeSlot|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeSlot|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeSlot[]    findAll()
 * @method TimeSlot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeSlotRepository extends ServiceEntityRepository
{
  private $manager;

  public function __construct
  (
      ManagerRegistry $registry,
      EntityManagerInterface $manager
  )
  {
      parent::__construct($registry, TimeSlot::class);
      $this->manager = $manager;
  }

  public function getAllTimeSlot() {
      $query = $this->createQueryBuilder('e');
      $query->where('e.isDeleted IS NULL');
      $query->orWhere('e.isDeleted = false');
      $data = $query->orderBy('e.id', 'ASC')
          ->getQuery()->getResult();
      return (object) $data;
    }

  public function saveTimeSlot($timeSlotData)
  {
      $timeSlot = new TimeSlot();
      $timeSlot->setTime($timeSlotData->time);

      $this->manager->persist($timeSlot);
      $this->manager->flush();
  }

  public function updateTimeSlot(TimeSlot $timeSlot, $data)
  {
      empty($data->time) ? true : $timeSlot->setTime($data->time);

      $this->manager->flush();
  }

  public function removeTimeSlot(TimeSlot $timeSlot)
  {
    $timeSlot->setIsDeleted(true);

    $this->manager->flush();
  }
}
