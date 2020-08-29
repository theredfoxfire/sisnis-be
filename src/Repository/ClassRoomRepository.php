<?php

namespace App\Repository;

use App\Entity\ClassRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method ClassRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClassRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClassRoom[]    findAll()
 * @method ClassRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassRoomRepository extends ServiceEntityRepository
{
  private $manager;

  public function __construct
  (
      ManagerRegistry $registry,
      EntityManagerInterface $manager
  )
  {
      parent::__construct($registry, ClassRoom::class);
      $this->manager = $manager;
  }

  public function saveClassRoom($name)
  {
      $classRoom = new ClassRoom();

      $classRoom->setName($name);

      $this->manager->persist($classRoom);
      $this->manager->flush();
  }

  public function updateClassRoom(ClassRoom $classRooms, $data)
  {
      empty($data['name']) ? true : $classRooms->setName($data['name']);

      $this->manager->flush();
  }

  public function removeClassRoom(ClassRoom $classRooms)
  {
      $this->manager->remove($classRooms);
      $this->manager->flush();
  }
}
