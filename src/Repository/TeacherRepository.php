<?php

namespace App\Repository;

use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Teacher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Teacher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Teacher[]    findAll()
 * @method Teacher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeacherRepository extends ServiceEntityRepository
{
  private $manager;

  public function __construct
  (
      ManagerRegistry $registry,
      EntityManagerInterface $manager
  )
  {
      parent::__construct($registry, Teacher::class);
      $this->manager = $manager;
  }

  public function saveTeacher($teacherData)
  {
      $teacher = new Teacher();

      $teacher->setSerial($teacherData->serial);
      $teacher->setName($teacherData->name);

      $this->manager->persist($teacher);
      $this->manager->flush();
  }

  public function updateTeacher(Teacher $teacher, $data)
  {
      empty($data['serial']) ? true : $teacher->setSerial($data['serial']);
      empty($data['name']) ? true : $teacher->setName($data['name']);

      $this->manager->flush();
  }

  public function removeTeacher(Teacher $teacher)
  {
      $this->manager->remove($teacher);
      $this->manager->flush();
  }
}
