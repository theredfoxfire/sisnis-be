<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoomSiteController
 * @package App\Controller
 *
 * @Route(path="/api/room")
 */
class RoomController
{
    private $roomRepository;

    public function __construct(RoomRepository $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    /**
     * @Route("/add", name="add_room", methods={"POST"})
     */
    public function addRoom(Request $request): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->name)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->roomRepository->saveRoom($data);

        return new JsonResponse(['status' => 'Room added!'], Response::HTTP_OK);
    }

    /**
     * @Route("/get/{id}", name="get_one_room", methods={"GET"})
     */
    public function getOneRoom($id): JsonResponse
    {
        $room = $this->roomRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $room->getId(),
            'name' => $room->getName(),
        ];

        return new JsonResponse(['room' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_rooms", methods={"GET"})
     */
    public function getAllRooms(): JsonResponse
    {
        $rooms = $this->roomRepository->getAllRoom();
        $data = [];

        foreach ($rooms as $room) {
            $data[] = [
                'id' => $room->getId(),
                'name' => $room->getName(),
            ];
        }

        return new JsonResponse(['rooms' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_room", methods={"PUT"})
     */
    public function updateRoom($id, Request $request): JsonResponse
    {
        $room = $this->roomRepository->findOneBy(['id' => $id]);
        $data = (object)json_decode($request->getContent(), true);

        $this->roomRepository->updateRoom($room, $data);

        return new JsonResponse(['status' => 'room updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", name="delete_room", methods={"DELETE"})
     */
    public function deleteRoom($id): JsonResponse
    {
        $room = $this->roomRepository->findOneBy(['id' => $id]);

        $this->roomRepository->removeRoom($room);

        return new JsonResponse(['status' => 'room deleted'], Response::HTTP_OK);
    }
}
