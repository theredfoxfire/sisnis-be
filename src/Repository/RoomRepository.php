<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository
{
  private $manager;

  public function __construct
  (
      ManagerRegistry $registry,
      EntityManagerInterface $manager
  )
  {
      parent::__construct($registry, Room::class);
      $this->manager = $manager;
  }

  public function getAllRoom() {
      $query = $this->createQueryBuilder('e');
      $query->where('e.isDeleted IS NULL');
      $query->orWhere('e.isDeleted = false');
      $data = $query->orderBy('e.id', 'ASC')
          ->getQuery()->getResult();
      return (object) $data;
    }

  public function saveRoom($roomData)
  {
      $room = new Room();
      $room->setName($roomData->name);

      $this->manager->persist($room);
      $this->manager->flush();
  }

  public function updateRoom(Room $room, $data)
  {
      empty($data->name) ? true : $room->setName($data->name);

      $this->manager->flush();
  }

  public function removeRoom(Room $room)
  {
    $room->setIsDeleted(true);

    $this->manager->flush();
  }
}
