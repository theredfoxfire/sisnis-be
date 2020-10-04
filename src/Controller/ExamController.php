<?php

namespace App\Controller;

use App\Repository\ExamRepository;
use App\Repository\StudentRepository;
use App\Repository\ExamTypeRepository;
use App\Repository\TeacherClassToSubjectRepository;
use App\Repository\ExamPointRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ExamSiteController
 * @package App\Controller
 *
 * @Route(path="/api/exam")
 */
class ExamController
{
    private $examRepository;
    private $studentRepository;
    private $examPointRepository;
    private $examTypeRepository;
    private $teacherClassToSubjectRepository;

    public function __construct(
        StudentRepository $studentRepository,
        ExamRepository $examRepository,
        ExamPointRepository $examPointRepository,
        ExamTypeRepository $examTypeRepository,
        TeacherClassToSubjectRepository $teacherClassToSubjectRepository
    )
    {
        $this->examRepository = $examRepository;
        $this->examPointRepository = $examPointRepository;
        $this->examTypeRepository = $examTypeRepository;
        $this->studentRepository = $studentRepository;
        $this->teacherClassToSubjectRepository = $teacherClassToSubjectRepository;
    }

    /**
     * @Route("/add", name="add_exam", methods={"POST"})
     */
    public function addExam(Request $request): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->name) || empty($data->teacherSubject) || empty($data->examType) || empty($data->examDate)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $teacherSubject = $this->teacherClassToSubjectRepository->findOneBy(['id' => $data->teacherSubject]);
        $examType = $this->examTypeRepository->findOneBy(['id' => $data->examType]);

        $this->examRepository->saveExam($data, $teacherSubject, $examType);

        return new JsonResponse(['status' => 'Exam added!'], Response::HTTP_OK);
    }

    /**
     * @Route("/point/add", name="add_point_exam", methods={"POST"})
     */
    public function addExamPoint(Request $request): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->examId) || empty($data->studentPoints)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        foreach ($data->studentPoints as $key => $value) {
            $exam = $this->examRepository->findOneBy(['id' => $data->examId]);
            $student = $this->studentRepository->findOneBy(['id' => $value['id']]);
            $this->examPointRepository->saveExamPoint($value['point'], $exam, $student);
        }

        return new JsonResponse(['status' => 'Exam added!'], Response::HTTP_OK);
    }

    /**
     * @Route("/point/update", name="update_point_exam", methods={"PUT"})
     */
    public function updateExamPoint(Request $request): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->examId) || empty($data->studentPoints)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        foreach ($data->studentPoints as $key => $value) {
            $examPoint = $this->examPointRepository->findOneBy(['id' => $value['pointId']]);
            $this->examPointRepository->updateExamPoint($examPoint, $value);
        }

        return new JsonResponse(['status' => 'Exam Updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/get/{id}", name="get_one_exam", methods={"GET"})
     */
    public function getOneExam($id): JsonResponse
    {
        $exam = $this->examRepository->findOneBy(['id' => $id]);
        $students = [];
        $examPoints = [];
        foreach ($exam->getTeacherSubject()->getClassRoom()->getStudents() as $key => $value) {
            $students[$key] = [
                'studentId' => $value->getId(),
                'studentName' => $value->getName(),
            ];
        }

        foreach ($exam->getExamPoints() as $key => $value) {
            $examPoints[$key] = [
                'id' => $value->getStudent()->getId(),
                'point' => $value->getPoint(),
                'pointId' => $value->getId(),
            ];
        }

        $data = [
            'id' => $exam->getId(),
            'name' => $exam->getName(),
            'date' => $exam->getDate(),
            'teacherSubjectId' => $exam->getTeacherSubject()->getId(),
            'classRoomId' => $exam->getTeacherSubject()->getClassRoom()->getId(),
            'classRoomName' => $exam->getTeacherSubject()->getClassRoom()->getName(),
            'subjectId' => $exam->getTeacherSubject()->getSubject()->getId(),
            'subjectName' => $exam->getTeacherSubject()->getSubject()->getName(),
            'teacherId' => $exam->getTeacherSubject()->getTeacher()->getId(),
            'teacherName' => $exam->getTeacherSubject()->getTeacher()->getName(),
            'students' => $students,
            'examPoints' => $examPoints,
        ];

        return new JsonResponse(['exam' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get/teacher-subject/{id}", name="get_teacher_subject", methods={"GET"})
     */
    public function getTeacherSubject($id): JsonResponse
    {
        $teacherSubject = $this->teacherClassToSubjectRepository->findOneBy(['id' => $id]);
        $exams = [];
        foreach ($teacherSubject->getExams() as $key => $value) {
            $exams[$key] = array_merge($value->toArray(), $value->getExamType()->toArray());
        }

        $data = [
            'id' => $teacherSubject->getId(),
            'name' => $teacherSubject->getSubject()->getName(),
            'className' => $teacherSubject->getClassRoom()->getName(),
            'teacherName' => $teacherSubject->getTeacher()->getName(),
            'teacherId' => $teacherSubject->getTeacher()->getId(),
            'exams' => $exams,
        ];

        return new JsonResponse(['exam' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_exams", methods={"GET"})
     */
    public function getAllExams(): JsonResponse
    {
        $exams = $this->examRepository->findAll();
        $data = [];

        foreach ($exams as $exam) {
            $data[] = $exam->toArray();
        }

        return new JsonResponse(['exams' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", name="delete_exam", methods={"DELETE"})
     */
    public function deleteExam($id): JsonResponse
    {
        $exam = $this->examRepository->findOneBy(['id' => $id]);

        $this->examRepository->removeExam($exam);

        return new JsonResponse(['status' => 'exam deleted'], Response::HTTP_OK);
    }
}
