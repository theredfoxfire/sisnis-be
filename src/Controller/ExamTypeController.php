<?php

namespace App\Controller;

use App\Repository\ExamTypeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ExamTypeSiteController
 * @package App\Controller
 *
 * @Route(path="/api/examType")
 */
class ExamTypeController
{
    private $examTypeRepository;

    public function __construct(ExamTypeRepository $examTypeRepository)
    {
        $this->examTypeRepository = $examTypeRepository;
    }

    /**
     * @Route("/add", name="add_examType", methods={"POST"})
     */
    public function addExamType(Request $request): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->name) || empty($data->scale)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->examTypeRepository->saveExamType($data);

        return new JsonResponse(['status' => 'ExamType added!'], Response::HTTP_OK);
    }

    /**
     * @Route("/get/{id}", name="get_one_examType", methods={"GET"})
     */
    public function getOneExamType($id): JsonResponse
    {
        $examType = $this->examTypeRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $examType->getId(),
            'name' => $examType->getName(),
            'scale' => $examType->getScale(),
        ];

        return new JsonResponse(['examType' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_examTypes", methods={"GET"})
     */
    public function getAllExamTypes(): JsonResponse
    {
        $examTypes = $this->examTypeRepository->findAll();
        $data = [];

        foreach ($examTypes as $examType) {
            $data[] = [
                'id' => $examType->getId(),
                'name' => $examType->getName(),
                'scale' => $examType->getScale(),
            ];
        }

        return new JsonResponse(['examTypes' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_examType", methods={"PUT"})
     */
    public function updateExamType($id, Request $request): JsonResponse
    {
        $examType = $this->examTypeRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        $this->examTypeRepository->updateExamType($examType, $data);

        return new JsonResponse(['status' => 'examType updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", name="delete_examType", methods={"DELETE"})
     */
    public function deleteExamType($id): JsonResponse
    {
        $examType = $this->examTypeRepository->findOneBy(['id' => $id]);

        $this->examTypeRepository->removeExamType($examType);

        return new JsonResponse(['status' => 'examType deleted'], Response::HTTP_OK);
    }
}
