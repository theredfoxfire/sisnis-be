<?php

namespace App\Controller;

use App\Repository\AcademicYearRepository;
use App\Repository\ClassRoomRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AcademicYearSiteController
 * @package App\Controller
 *
 * @Route(path="/api/academic_year")
 */
class AcademicYearController
{
    private $academicYearRepository;
    private $classRoomRepository;

    public function __construct(AcademicYearRepository $academicYearRepository, ClassRoomRepository $classRoomRepository)
    {
        $this->academicYearRepository = $academicYearRepository;
        $this->classRoomRepository = $classRoomRepository;
    }

    /**
     * @Route("/add", name="add_academic_year", methods={"POST"})
     */
    public function addAcademicYear(Request $request): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->year)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->academicYearRepository->saveAcademicYear($data);

        return new JsonResponse(['status' => 'AcademicYear added!'], Response::HTTP_OK);
    }

    /**
     * @Route("/get/{id}", name="get_one_academic_year", methods={"GET"})
     */
    public function getOneAcademicYear($id): JsonResponse
    {
        $academicYear = $this->academicYearRepository->findOneBy(['id' => $id]);
        $data = $academicYear->toArray();

        return new JsonResponse(['academic_year' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_academic_years", methods={"GET"})
     */
    public function getAllAcademicYears(Request $request): JsonResponse
    {
        $currentPage = $request->query->get('page');
        $pageItems = $request->query->get('pageItems');
        $start = ($currentPage - 1) * $pageItems;
        $academicYears = $this->academicYearRepository->getAllAcademicYears($start, $pageItems);
        $data = [];

        foreach ($academicYears->data as $academicYear) {
            $data[] = $academicYear->toArray();
        }

        return new JsonResponse(['academic_years' => $data, 'totals' => $academicYears->totals], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_academic_year", methods={"PUT"})
     */
    public function updateAcademicYear($id, Request $request): JsonResponse
    {
        $academicYear = $this->academicYearRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        $this->academicYearRepository->updateAcademicYear($academicYear, $data);

        return new JsonResponse(['status' => 'academic_year updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", name="delete_academic_year", methods={"DELETE"})
     */
    public function deleteAcademicYear($id): JsonResponse
    {
        $academicYear = $this->academicYearRepository->findOneBy(['id' => $id]);

        $this->academicYearRepository->removeAcademicYear($academicYear);

        return new JsonResponse(['status' => 'academic_year deleted'], Response::HTTP_OK);
    }
}
