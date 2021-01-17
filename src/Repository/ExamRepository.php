<?php

namespace App\Repository;

use App\Entity\Exam;
use App\Entity\TeacherClassToSubject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Exam|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exam|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exam[]    findAll()
 * @method Exam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamRepository extends ServiceEntityRepository
{
  private $manager;

  public function __construct
  (
      ManagerRegistry $registry,
      EntityManagerInterface $manager
  )
  {
      parent::__construct($registry, Exam::class);
      $this->manager = $manager;
  }

  public function saveExam($examData, TeacherClassToSubject $teacherSubject, $examType)
  {
      $exam = new Exam();

      $exam->setTeacherSubject($teacherSubject);
      $exam->setName($examData->name);
      $exam->setDate($examData->examDate);
      $exam->setExamType($examType);

      $this->manager->persist($exam);
      $this->manager->flush();
  }

  public function updateExam(Exam $exam, $data)
  {
      empty($data['serial']) ? true : $exam->setSerial($data['serial']);
      empty($data['name']) ? true : $exam->setName($data['name']);

      $this->manager->flush();
  }

  public function updateExamTeacherClassToSubject(Exam $exam)
  {
      $this->manager->flush();
  }

  public function removeExam(Exam $exam)
  {
    $exam->setIsDeleted(true);

    $this->manager->flush();
  }
}
