<?php

namespace App\Controller;

use App\Repository\TimeSlotRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TimeSlotSiteController
 * @package App\Controller
 *
 * @Route(path="/api/timeSlot")
 */
class TimeSlotController
{
    private $timeSlotRepository;

    public function __construct(TimeSlotRepository $timeSlotRepository)
    {
        $this->timeSlotRepository = $timeSlotRepository;
    }

    /**
     * @Route("/add", name="add_timeSlot", methods={"POST"})
     */
    public function addTimeSlot(Request $request): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->time)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->timeSlotRepository->saveTimeSlot($data);

        return new JsonResponse(['status' => 'TimeSlot added!'], Response::HTTP_OK);
    }

    /**
     * @Route("/get/{id}", name="get_one_timeSlot", methods={"GET"})
     */
    public function getOneTimeSlot($id): JsonResponse
    {
        $timeSlot = $this->timeSlotRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $timeSlot->getId(),
            'time' => $timeSlot->getTime(),
        ];

        return new JsonResponse(['timeSlot' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_timeSlots", methods={"GET"})
     */
    public function getAllTimeSlots(): JsonResponse
    {
        $timeSlots = $this->timeSlotRepository->getAllTimeSlot();
        $data = [];

        foreach ($timeSlots as $timeSlot) {
            $data[] = [
                'id' => $timeSlot->getId(),
                'time' => $timeSlot->getTime(),
            ];
        }

        return new JsonResponse(['timeSlots' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_timeSlot", methods={"PUT"})
     */
    public function updateTimeSlot($id, Request $request): JsonResponse
    {
        $timeSlot = $this->timeSlotRepository->findOneBy(['id' => $id]);
        $data = (object)json_decode($request->getContent(), true);

        $this->timeSlotRepository->updateTimeSlot($timeSlot, $data);

        return new JsonResponse(['status' => 'timeSlot updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", name="delete_timeSlot", methods={"DELETE"})
     */
    public function deleteTimeSlot($id): JsonResponse
    {
        $timeSlot = $this->timeSlotRepository->findOneBy(['id' => $id]);

        $this->timeSlotRepository->removeTimeSlot($timeSlot);

        return new JsonResponse(['status' => 'timeSlot deleted'], Response::HTTP_OK);
    }
}
