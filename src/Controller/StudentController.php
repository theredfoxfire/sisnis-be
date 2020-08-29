<?php

namespace App\Controller;

use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StudentSiteController
 * @package App\Controller
 *
 * @Route(path="/api/student")
 */
class StudentController
{
    private $studentRepository;

    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    /**
     * @Route("/add", name="add_student", methods={"POST"})
     */
    public function addStudent(Request $request): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->name) || empty($data->serial)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->studentRepository->saveStudent($data);

        return new JsonResponse(['status' => 'Student added!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/get/{id}", name="get_one_student", methods={"GET"})
     */
    public function getOneStudent($id): JsonResponse
    {
        $student = $this->studentRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $student->getId(),
            'name' => $student->getName(),
            'serial' => $student->getSerial(),
        ];

        return new JsonResponse(['student' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_students", methods={"GET"})
     */
    public function getAllStudents(): JsonResponse
    {
        $students = $this->studentRepository->findAll();
        $data = [];

        foreach ($students as $student) {
            $data[] = [
                'id' => $student->getId(),
                'name' => $student->getName(),
                'serial' => $student->getSerial(),
            ];
        }

        return new JsonResponse(['students' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_student", methods={"PUT"})
     */
    public function updateStudent($id, Request $request): JsonResponse
    {
        $student = $this->studentRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        $this->studentRepository->updateStudent($student, $data);

        return new JsonResponse(['status' => 'student updated!']);
    }

    /**
     * @Route("/delete/{id}", name="delete_student", methods={"DELETE"})
     */
    public function deleteStudent($id): JsonResponse
    {
        $student = $this->studentRepository->findOneBy(['id' => $id]);

        $this->studentRepository->removeStudent($student);

        return new JsonResponse(['status' => 'student deleted']);
    }
}
