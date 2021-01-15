<?php

namespace App\Repository;

use App\Entity\ClassRoom;
use App\Entity\Teacher;
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

  public function getAllClassRooms() {
    $query = $this->createQueryBuilder('e');
    $query->where('e.isDeleted IS NULL');
    $query->orWhere('e.isDeleted = false');
    $data = $query->orderBy('e.id', 'ASC')
        ->getQuery()->getResult();
    return (object) $data;
  }

  public function saveClassRoom($name, Teacher $teacher)
  {
      $classRoom = new ClassRoom();

      $classRoom->setName($name);
      $classRoom->setGuardian($teacher);

      $this->manager->persist($classRoom);
      $this->manager->flush();
  }

  public function updateClassRoom(ClassRoom $classRooms, $data, Teacher $teacher)
  {
      empty($data['name']) ? true : $classRooms->setName($data['name']);
      $classRooms->setGuardian($teacher);

      $this->manager->flush();
  }

  public function setGuardianClass(ClassRoom $classRooms, Teacher $teacher)
  {
      $classRooms->setGuardian($teacher);

      $this->manager->flush();
  }

  public function removeClassRoom(ClassRoom $classRooms)
  {
    $classRooms->setIsDeleted(true);

    $this->manager->flush();
  }
}
