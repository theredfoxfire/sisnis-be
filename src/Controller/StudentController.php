<?php

namespace App\Controller;

use App\Repository\StudentRepository;
use App\Repository\ClassRoomRepository;
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
    private $classRoomRepository;

    public function __construct(StudentRepository $studentRepository, ClassRoomRepository $classRoomRepository)
    {
        $this->studentRepository = $studentRepository;
        $this->classRoomRepository = $classRoomRepository;
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

        return new JsonResponse(['status' => 'Student added!'], Response::HTTP_OK);
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
     * @Route("/add/class-room/{id}", name="add_student_class", methods={"PUT"})
     */
    public function addStudentClassRoom($id, Request $request): JsonResponse
    {
        $student = $this->studentRepository->findOneBy(['id' => $id]);
        $data = (object) json_decode($request->getContent(), true);
        if (empty($data->classRoomId)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $student->setClassRoom($this->classRoomRepository->findOneBy(['id' => $data->classRoomId]));
        $this->studentRepository->updateStudentClassRoom($student, $data);

        return new JsonResponse(['status' => 'student updated!'], Response::HTTP_OK);
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

        return new JsonResponse(['status' => 'student updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", name="delete_student", methods={"DELETE"})
     */
    public function deleteStudent($id): JsonResponse
    {
        $student = $this->studentRepository->findOneBy(['id' => $id]);

        $this->studentRepository->removeStudent($student);

        return new JsonResponse(['status' => 'student deleted'], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/class/{id}", name="delete_student_class", methods={"DELETE"})
     */
    public function deleteStudentClass($id): JsonResponse
    {
        $student = $this->studentRepository->findOneBy(['id' => $id]);
        $student->setClassRoom(null);
        $this->studentRepository->updateStudentClassRoom($student);

        return new JsonResponse(['status' => 'class-room deleted'], Response::HTTP_OK);
    }
}
