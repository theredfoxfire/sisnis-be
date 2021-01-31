<?php

namespace App\Repository;

use App\Entity\StudentAttendance;
use App\Entity\Student;
use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method StudentAttendance|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentAttendance|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentAttendance[]    findAll()
 * @method StudentAttendance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentAttendanceRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $manager
    ) {
        parent::__construct($registry, StudentAttendance::class);
        $this->manager = $manager;
    }

    public function getAllStudentAttendance(Schedule $schedule, $date)
    {
        $query = $this->createQueryBuilder('e');
        $query->where('e.isDeleted IS NULL');
        $query->orWhere('e.isDeleted = false');
        $query->andWhere('e.schedule = :schedule')->setParameter("schedule", $schedule);
        if (!empty($date && $date != 0)) {
            $query->andWhere('e.date = :date')->setParameter("date", $date);
        } else {
            $query->groupby('e.date');
        }
        $data = $query->orderBy('e.id', 'ASC')
          ->getQuery()->getResult();
        return (object) $data;
    }
    public function checkIsExist(Schedule $schedule, $date)
    {
        $query = $this->createQueryBuilder('e');
        $query->where('e.isDeleted IS NULL');
        $query->orWhere('e.isDeleted = false');
        $query->andWhere('e.schedule = :schedule')->setParameter("schedule", $schedule);
        $query->andWhere('e.date = :date')->setParameter("date", $date);
            $data = $query->orderBy('e.id', 'ASC')
          ->getQuery()->getResult();
        return !empty($data[0]);
    }

    public function saveStudentAttendance($studentAttendanceData, Schedule $schedule, Student $student, $date)
    {
        $studentAttendance = new StudentAttendance();
        $studentAttendance->setStudent($student);
        $studentAttendance->setPresenceStatus($studentAttendanceData->presenceStatus);
        $studentAttendance->setSchedule($schedule);
        $studentAttendance->setNotes($studentAttendanceData->notes);
        $studentAttendance->setDate($date);

        $this->manager->persist($studentAttendance);
        $this->manager->flush();
    }

    public function updateStudentAttendance(StudentAttendance $studentAttendance, $formData, Schedule $schedule, Student $student, $date)
    {
        $studentAttendance->setNotes($formData->notes);
        $studentAttendance->setPresenceStatus($formData->presenceStatus);
        $studentAttendance->setDate($date);

        $this->manager->flush();
    }

    public function removeStudentAttendance(StudentAttendance $studentAttendance)
    {
        $studentAttendance->setIsDeleted(true);

        $this->manager->flush();
    }
}
