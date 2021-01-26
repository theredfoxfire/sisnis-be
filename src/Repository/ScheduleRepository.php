<?php

namespace App\Repository;

use App\Entity\Schedule;
use App\Entity\TimeSlot;
use App\Entity\Room;
use App\Entity\TeacherClassToSubject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Schedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Schedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Schedule[]    findAll()
 * @method Schedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $manager
    ) {
        parent::__construct($registry, Schedule::class);
        $this->manager = $manager;
    }

    public function getAllSchedule($start = 0, $max = 25)
    {
        $query = $this->createQueryBuilder('e');
        $query->where('e.isDeleted IS NULL');
        $query->orWhere('e.isDeleted = false');
        $data = $query->orderBy('e.id', 'ASC')
      ->setFirstResult($start)
      ->setMaxResults($max)
          ->getQuery()->getResult();
        $totals = $this->createQueryBuilder('e')
      ->where('e.isDeleted IS NULL')
      ->orWhere('e.isDeleted = false')
          ->getQuery()
          ->getResult();
        return (object) array('totals' => count($totals), 'data' => $data);
    }
    
    public function isScheduleExist(Room $room, TimeSlot $timeSlot, String $day)
    {
        $query = $this->createQueryBuilder('e');
        $query->where('e.isDeleted IS NULL');
        $query->orWhere('e.isDeleted = false');
        $query->andWhere('e.room = :room')->setParameter('room', $room);
        $query->andWhere('e.day = :day')->setParameter('day', $day);
        $query->andWhere('e.timeSlot = :time')->setParameter('time', $timeSlot);
        $data = $query->orderBy('e.id', 'ASC')
          ->getQuery()->getResult();
        return count($data) > 0;
    }

    public function saveSchedule(Room $room, TimeSlot $timeSlot, TeacherClassToSubject $teacherSubject, String $day)
    {
        $schedule = new Schedule();
        $teacher = $teacherSubject->getTeacher();
        $classRoom = $teacherSubject->getClassRoom();
        $subjectItem = $teacherSubject->getSubject();
        $academicYear = $teacherSubject->getAcademicYear();
        $schedule->setRoom($room);
        $schedule->setTimeSlot($timeSlot);
        $schedule->setDay($day);
        $schedule->setSubject($teacherSubject);
        $schedule->setTeacherName($teacher->getName());
        $schedule->setSubjectName($subjectItem->getName());
        $schedule->setClassRoomName($classRoom->getName());
        $schedule->setAcademicYear($academicYear->getYear());

        $this->manager->persist($schedule);
        $this->manager->flush();
    }

    public function updateSchedule(Schedule $schedule, Room $room, TimeSlot $timeSlot, TeacherClassToSubject $teacherSubject, String $day)
    {
        $schedule->setRoom($room);
        $schedule->setTimeSlot($timeSlot);
        $schedule->setDay($day);
        $schedule->setSubject($teacherSubject);

        $this->manager->flush();
    }

    public function removeSchedule(Schedule $schedule)
    {
        $schedule->setIsDeleted(true);

        $this->manager->flush();
    }
}
