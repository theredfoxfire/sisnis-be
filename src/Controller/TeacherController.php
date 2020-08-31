<?php

namespace App\Controller;

use App\Repository\TeacherRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TeacherSiteController
 * @package App\Controller
 *
 * @Route(path="/api/teacher")
 */
class TeacherController
{
    private $teacherRepository;

    public function __construct(TeacherRepository $teacherRepository)
    {
        $this->teacherRepository = $teacherRepository;
    }

    /**
     * @Route("/add", name="add_teacher", methods={"POST"})
     */
    public function addTeacher(Request $request): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->name) || empty($data->serial)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->teacherRepository->saveTeacher($data);

        return new JsonResponse(['status' => 'Teacher added!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/get/{id}", name="get_one_teacher", methods={"GET"})
     */
    public function getOneTeacher($id): JsonResponse
    {
        $teacher = $this->teacherRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $teacher->getId(),
            'name' => $teacher->getName(),
            'serial' => $teacher->getSerial(),
        ];

        return new JsonResponse(['teacher' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_teachers", methods={"GET"})
     */
    public function getAllTeachers(): JsonResponse
    {
        $teachers = $this->teacherRepository->findAll();
        $data = [];

        foreach ($teachers as $teacher) {
            $data[] = [
                'id' => $teacher->getId(),
                'name' => $teacher->getName(),
                'serial' => $teacher->getSerial(),
            ];
        }

        return new JsonResponse(['teachers' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_teacher", methods={"PUT"})
     */
    public function updateTeacher($id, Request $request): JsonResponse
    {
        $teacher = $this->teacherRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        $this->teacherRepository->updateTeacher($teacher, $data);

        return new JsonResponse(['status' => 'teacher updated!']);
    }

    /**
     * @Route("/delete/{id}", name="delete_teacher", methods={"DELETE"})
     */
    public function deleteTeacher($id): JsonResponse
    {
        $teacher = $this->teacherRepository->findOneBy(['id' => $id]);

        $this->teacherRepository->removeTeacher($teacher);

        return new JsonResponse(['status' => 'teacher deleted']);
    }
}
