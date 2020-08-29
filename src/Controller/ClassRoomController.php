<?php

namespace App\Controller;

use App\Repository\ClassRoomRepository;
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

    public function __construct(ClassRoomRepository $classRoomRepository)
    {
        $this->classRoomRepository = $classRoomRepository;
    }

    /**
     * @Route("/add", name="add_class_room", methods={"POST"})
     */
    public function addClassRoom(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];

        if (empty($name)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->classRoomRepository->saveClassRoom($name);

        return new JsonResponse(['status' => 'ClassRoom added!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/get/{id}", name="get_one_class_room", methods={"GET"})
     */
    public function getOneClassRoom($id): JsonResponse
    {
        $classRoom = $this->classRoomRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $classRoom->getId(),
            'name' => $classRoom->getName(),
        ];

        return new JsonResponse(['classRoom' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_class_rooms", methods={"GET"})
     */
    public function getAllClassRooms(): JsonResponse
    {
        $classRooms = $this->classRoomRepository->findAll();
        $data = [];

        foreach ($classRooms as $classRoom) {
            $data[] = [
                'id' => $classRoom->getId(),
                'name' => $classRoom->getName(),
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

        $this->classRoomRepository->updateClassRoom($classRoom, $data);

        return new JsonResponse(['status' => 'class-room updated!']);
    }

    /**
     * @Route("/delete/{id}", name="delete_class_room", methods={"DELETE"})
     */
    public function deleteClassRoom($id): JsonResponse
    {
        $classRoom = $this->classRoomRepository->findOneBy(['id' => $id]);

        $this->classRoomRepository->removeClassRoom($classRoom);

        return new JsonResponse(['status' => 'class-room deleted']);
    }
}
