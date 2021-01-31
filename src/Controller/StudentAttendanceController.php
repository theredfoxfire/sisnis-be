<?php

namespace App\Controller;

use App\Repository\StudentAttendanceRepository;
use App\Repository\ScheduleRepository;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StudentAttendanceSiteController
 * @package App\Controller
 *
 * @Route(path="/api/studentAttendance")
 */
class StudentAttendanceController
{
    private $studentAttendanceRepository;
    private $scheduleRepository;
    private $studentRepository;

    public function __construct(StudentRepository $studentRepository, StudentAttendanceRepository $studentAttendanceRepository, ScheduleRepository $scheduleRepository)
    {
        $this->studentAttendanceRepository = $studentAttendanceRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->studentRepository = $studentRepository;
    }

    /**
     * @Route("/add", name="add_studentAttendance", methods={"POST"})
     */
    public function addStudentAttendance(Request $request): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->students) || empty($data->date)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $schedule = $this->scheduleRepository->findOneBy(['id' => $data->students[0]['schedule']]);
        $isExist = $this->studentAttendanceRepository->checkIsExist($schedule, $data->date);
        if (empty($data->students[0]['id']) && $isExist) {
            throw new NotFoundHttpException('Date attendance already exist!');
        }
        foreach ($data->students as $key => $value) {
            $item = (object)$value;
            $student = $this->studentRepository->findOneBy(['id' => $item->student]);
            $schedule = $this->scheduleRepository->findOneBy(['id' => $item->schedule]);
            if (!empty($item->id)) {
                $studentAttendance = $this->studentAttendanceRepository->findOneBy(['id' => $item->id]);
                $this->studentAttendanceRepository->updateStudentAttendance($studentAttendance, $item, $schedule, $student, $data->date);
            }

            if ($student && $schedule && empty($item->id)) {
                $this->studentAttendanceRepository->saveStudentAttendance($item, $schedule, $student, $data->date);
            }
        }

        return new JsonResponse(['status' => 'StudentAttendance added!'], Response::HTTP_OK);
    }

    /**
     * @Route("/get/{id}", name="get_one_studentAttendance", methods={"GET"})
     */
    public function getOneStudentAttendance($id): JsonResponse
    {
        $studentAttendance = $this->studentAttendanceRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $studentAttendance->getId(),
            'student' => $studentAttendance->getStudent()->toArray(),
            'schedule' => $studentAttendance->getSchedule()->toArray(),
            'presenceStatus' => $studentAttendance->getPresenceStatus(),
            'notes' => $studentAttendance->getNotes(),
            'date' => $studentAttendance->getDate(),
        ];

        return new JsonResponse(['studentAttendance' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all/{scheduleId}", name="get_all_studentAttendances", methods={"GET"})
     */
    public function getAllStudentAttendances($scheduleId): JsonResponse
    {
        $schedule = $this->scheduleRepository->findOneBy(['id' => $scheduleId]);
        $studentAttendances = $this->studentAttendanceRepository->getAllStudentAttendance($schedule);
        $data = [];

        foreach ($studentAttendances as $studentAttendance) {
            $data[] = [
                'id' => $studentAttendance->getId(),
                'student' => $studentAttendance->getStudent()->getId(),
                'schedule' => $studentAttendance->getSchedule()->getId(),
                'presenceStatus' => $studentAttendance->getPresenceStatus(),
                'notes' => $studentAttendance->getNotes(),
                'date' => $studentAttendance->getDate(),
            ];
        }

        return new JsonResponse(['studentAttendances' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all/{scheduleId}/{date}", name="get_all_studentAttendances", methods={"GET"})
     */
    public function getAllAttendancesByDate($scheduleId, $date): JsonResponse
    {
        $schedule = $this->scheduleRepository->findOneBy(['id' => $scheduleId]);
        $studentAttendances = $this->studentAttendanceRepository->getAllStudentAttendance($schedule, $date);
        $data = [];

        foreach ($studentAttendances as $studentAttendance) {
            $data[] = [
                'id' => $studentAttendance->getId(),
                'student' => $studentAttendance->getStudent()->getId(),
                'schedule' => $studentAttendance->getSchedule()->getId(),
                'presenceStatus' => $studentAttendance->getPresenceStatus(),
                'notes' => $studentAttendance->getNotes(),
                'date' => $studentAttendance->getDate(),
            ];
        }

        return new JsonResponse(['studentAttendances' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/update", name="update_studentAttendance", methods={"PUT"})
     */
    public function updateStudentAttendance($id, Request $request): JsonResponse
    {
        $studentAttendance = $this->studentAttendanceRepository->findOneBy(['id' => $id]);
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->student) || empty($data->schedule) || empty($data->student) || empty($data->presenceStatus) || empty($data->notes)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->studentAttendanceRepository->updateStudentAttendance($studentAttendance, $data);

        return new JsonResponse(['status' => 'studentAttendance updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", name="delete_studentAttendance", methods={"DELETE"})
     */
    public function deleteStudentAttendance($id): JsonResponse
    {
        $studentAttendance = $this->studentAttendanceRepository->findOneBy(['id' => $id]);

        $this->studentAttendanceRepository->removeStudentAttendance($studentAttendance);

        return new JsonResponse(['status' => 'studentAttendance deleted'], Response::HTTP_OK);
    }
}
