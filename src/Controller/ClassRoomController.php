<?php

namespace App\Controller;

use App\Repository\ClassRoomRepository;
use App\Repository\TeacherRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ClassRoomSiteController
 * @package App\Controller
 *
 * @Route(path="/api/class-room")
 */
class ClassRoomController
{
    private $classRoomRepository;
    private $teacherRepository;

    public function __construct(ClassRoomRepository $classRoomRepository, TeacherRepository $teacherRepository)
    {
        $this->classRoomRepository = $classRoomRepository;
        $this->teacherRepository = $teacherRepository;
    }

    /**
     * @Route("/add", name="add_class_room", methods={"POST"})
     */
    public function addClassRoom(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name']) || empty($data['teacherId'])) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $teacher = $this->teacherRepository->findOneBy(['id' => $data['teacherId']]);
        $this->classRoomRepository->saveClassRoom($data['name'], $teacher);

        return new JsonResponse(['status' => 'ClassRoom added!'], Response::HTTP_OK);
    }

    /**
     * @Route("/get/{id}", name="get_one_class_room", methods={"GET"})
     */
    public function getOneClassRoom($id): JsonResponse
    {
        $classRoom = $this->classRoomRepository->findOneBy(['id' => $id]);
        if (empty($classRoom)) {
            throw new NotFoundHttpException('Invalid class room ID!');
        }
        $students = [];
        foreach ($classRoom->getStudents() as $key => $value) {
            $students[$key] = $value->toArray();
        }
        $guardian = $classRoom->getGuardian();
        $data = [
            'id' => $classRoom->getId(),
            'name' => $classRoom->getName(),
            'teacherId' => $guardian ? $guardian->getId() : 0,
            'guardianName' => $guardian ? $guardian->getName() : "",
            'students' => $students,
        ];

        return new JsonResponse(['classRoom' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_class_rooms", methods={"GET"})
     */
    public function getAllClassRooms(): JsonResponse
    {
        $classRooms = $this->classRoomRepository->getAllClassRooms();
        $data = [];
        foreach ($classRooms as $classRoom) {
            $guardian = $classRoom->getGuardian();
            $data[] = [
                'id' => $classRoom->getId(),
                'name' => $classRoom->getName(),
                'teacherId' => $guardian ? $guardian->getId() : 0,
                'guardianName' => $guardian ? $guardian->getName() : "---",
            ];
        }

        return new JsonResponse(['classRooms' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_class_room", methods={"PUT"})
     */
    public function updateClassRoom($id, Request $request): JsonResponse
    {
        $classRoom = $this->classRoomRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);
        $teacher = $this->teacherRepository->findOneBy(['id' => $data['teacherId']]);

        $this->classRoomRepository->updateClassRoom($classRoom, $data, $teacher);

        return new JsonResponse(['status' => 'class-room updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/set-guardian/{id}", name="set_guardian_class_room", methods={"PUT"})
     */
    public function setGuardianClass($id, Request $request): JsonResponse
    {
        $classRoom = $this->classRoomRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);
        $teacher = $this->teacherRepository->findOneBy(['id' => $data['teacherId']]);

        $this->classRoomRepository->setGuardianClass($classRoom, $teacher);

        return new JsonResponse(['status' => 'class-room updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", name="delete_class_room", methods={"DELETE"})
     */
    public function deleteClassRoom($id): JsonResponse
    {
        $classRoom = $this->classRoomRepository->findOneBy(['id' => $id]);

        $this->classRoomRepository->removeClassRoom($classRoom);

        return new JsonResponse(['status' => 'class-room deleted'], Response::HTTP_OK);
    }
}
