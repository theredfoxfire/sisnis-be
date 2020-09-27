<?php

namespace App\Controller;

use App\Repository\ExamRepository;
use App\Repository\TeacherClassToSubjectRepository;
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
    private $teacherClassToSubjectRepository;

    public function __construct(ExamRepository $examRepository, TeacherClassToSubjectRepository $teacherClassToSubjectRepository)
    {
        $this->examRepository = $examRepository;
        $this->teacherClassToSubjectRepository = $teacherClassToSubjectRepository;
    }

    /**
     * @Route("/add", name="add_exam", methods={"POST"})
     */
    public function addExam(Request $request): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->name) || empty($data->teacherSubject)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $teacherSubject = $this->teacherClassToSubjectRepository->findOneBy(['id' => $data->teacherSubject]);

        $this->examRepository->saveExam($data, $teacherSubject);

        return new JsonResponse(['status' => 'Exam added!'], Response::HTTP_OK);
    }

    /**
     * @Route("/get/{id}", name="get_one_exam", methods={"GET"})
     */
    public function getOneExam($id): JsonResponse
    {
        $exam = $this->examRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $exam->getId(),
            'name' => $exam->getName(),
            'serial' => $exam->getSerial(),
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
            $exams[$key] = $value->toArray();
        }

        $data = [
            'id' => $teacherSubject->getId(),
            'name' => $teacherSubject->getSubject()->getName(),
            'className' => $teacherSubject->getClassRoom()->getName(),
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
