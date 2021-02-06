<?php

namespace App\Controller;

use App\Repository\ScheduleRepository;
use App\Repository\TimeSlotRepository;
use App\Repository\RoomRepository;
use App\Repository\TeacherClassToSubjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ScheduleSiteController
 * @package App\Controller
 *
 * @Route(path="/api/schedule")
 */
class ScheduleController
{
    private $scheduleRepository;
    private $timeSlotRepository;
    private $roomRepository;
    private $teacherClassToSubjectRepository;

    public function __construct(
        ScheduleRepository $scheduleRepository,
        TimeSlotRepository $timeSlotRepository,
        RoomRepository $roomRepository,
        TeacherClassToSubjectRepository $teacherClassToSubjectRepository
    )
    {
        $this->scheduleRepository = $scheduleRepository;
        $this->timeSlotRepository = $timeSlotRepository;
        $this->roomRepository = $roomRepository;
        $this->teacherClassToSubjectRepository = $teacherClassToSubjectRepository;
    }

    /**
     * @Route("/add", name="add_schedule", methods={"POST"})
     */
    public function addSchedule(Request $request): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->time) || empty($data->room) || empty($data->subject) || empty($data->day)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $room = $this->roomRepository->findOneBy(['id' => $data->room]);
        $timeSlot = $this->timeSlotRepository->findOneBy(['id' => $data->time]);
        $teacherSubject = $this->teacherClassToSubjectRepository->findOneBy(['id' => $data->subject]);
        if ($this->scheduleRepository->isScheduleExist($room, $timeSlot, $data->day)) {
            throw new NotFoundHttpException('Schedule already booked!');
        }
        $this->scheduleRepository->saveSchedule($room, $timeSlot, $teacherSubject, $data->day);

        return new JsonResponse(['status' => 'Schedule added!'], Response::HTTP_OK);
    }

    /**
     * @Route("/get/{id}", name="get_one_schedule", methods={"GET"})
     */
    public function getOneSchedule($id): JsonResponse
    {
        $schedule = $this->scheduleRepository->findOneBy(['id' => $id]);
        $studentsMap = $schedule->getSubject()->getClassRoom()->getStudents();
        $students = [];
        foreach ($studentsMap as $key => $value) {
            $students[] = $value->toArray();
        }
        $data = [
            'id' => $schedule->getId(),
            'time' => $schedule->getTimeSlot()->getId(),
            'timeString' => $schedule->getTimeSlot()->getTime(),
            'day' => $schedule->getDay(),
            'room' => $schedule->getRoom()->getId(),
            'subject' => $schedule->getSubject()->getId(),
            'classRoomName' => $schedule->getClassRoomName(),
            'subjectName' => $schedule->getSubjectName(),
            'teacherName' => $schedule->getTeacherName(),
            'academicYear' => $schedule->getAcademicYear(),
            'students' => $students,
        ];

        return new JsonResponse(['schedule' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_schedules", methods={"GET"})
     */
    public function getAllSchedules(Request $request): JsonResponse
    {
        $currentPage = $request->query->get('page');
        $pageItems = $request->query->get('pageItems');
        $start = ($currentPage - 1) * $pageItems;
        $schedules = $this->scheduleRepository->getAllSchedule($start, $pageItems);
        $data = [];

        foreach ($schedules->data as $schedule) {
            $timeSlot = $schedule->getTimeSlot();
            $room = $schedule->getRoom();
            $subjectMap = $schedule->getSubject();
            $teacher = $subjectMap->getTeacher();
            $classRoom = $subjectMap->getClassRoom();
            $subjectItem = $subjectMap->getSubject();
            $academicYear = $subjectMap->getAcademicYear();

            $data[] = [
                'id' => $schedule->getId(),
                'time' => $timeSlot->toArray(),
                'room' => $room->toArray(),
                'teacher' => $teacher->toArray(),
                'day' => $schedule->getDay(),
                'classRoom' => $classRoom->toArray(),
                'subject' => $subjectItem->toArray(),
                'academicYear' => $academicYear->toArray(),
            ];
        }

        return new JsonResponse(['schedules' => $data, 'totals' => $schedules->totals], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_schedule", methods={"PUT"})
     */
    public function updateSchedule($id, Request $request): JsonResponse
    {
        $schedule = $this->scheduleRepository->findOneBy(['id' => $id]);
        $data = (object)json_decode($request->getContent(), true);
        if (empty($data->time) || empty($data->room) || empty($data->subject) || empty($data->day)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $room = $this->roomRepository->findOneBy(['id' => $data->room]);
        $timeSlot = $this->timeSlotRepository->findOneBy(['id' => $data->time]);
        $teacherSubject = $this->teacherClassToSubjectRepository->findOneBy(['id' => $data->subject]);
        $this->scheduleRepository->updateSchedule($schedule, $room, $timeSlot, $teacherSubject, $data->day);

        return new JsonResponse(['status' => 'schedule updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", name="delete_schedule", methods={"DELETE"})
     */
    public function deleteSchedule($id): JsonResponse
    {
        $schedule = $this->scheduleRepository->findOneBy(['id' => $id]);

        $this->scheduleRepository->removeSchedule($schedule);

        return new JsonResponse(['status' => 'schedule deleted'], Response::HTTP_OK);
    }
}
