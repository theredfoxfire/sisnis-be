<?php

namespace App\Controller;

use App\Repository\SchoolInfoRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SchoolInfoSiteController
 * @package App\Controller
 *
 * @Route(path="/api/schoolInfo")
 */
class SchoolInfoController
{
    private $schoolInfoRepository;

    public function __construct(SchoolInfoRepository $schoolInfoRepository)
    {
        $this->schoolInfoRepository = $schoolInfoRepository;
    }

    /**
     * @Route("/add", name="add_schoolInfo", methods={"POST"})
     */
    public function addSchoolInfo(Request $request): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->name) || empty($data->phone)
            || empty($data->email) || empty($data->address)
            || empty($data->province) || empty($data->city)
            || empty($data->subdistrict) || empty($data->postalCode)
            ) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->schoolInfoRepository->saveSchoolInfo($data);

        return new JsonResponse(['status' => 'SchoolInfo added!'], Response::HTTP_OK);
    }

    /**
     * @Route("/get/{id}", name="get_one_schoolInfo", methods={"GET"})
     */
    public function getOneSchoolInfo($id): JsonResponse
    {
        $schoolInfo = $this->schoolInfoRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $schoolInfo->getId(),
            'name' => $schoolInfo->getName(),
            'phone' => $schoolInfo->getPhone(),
            'email' => $schoolInfo->getEmail(),
            'address' => $schoolInfo->getAddress(),
            'province' => $schoolInfo->getProvince(),
            'city' => $schoolInfo->getCity(),
            'subdistrict' => $schoolInfo->getSubdistrict(),
            'postalCode' => $schoolInfo->getPostalCode(),
        ];

        return new JsonResponse(['schoolInfo' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_schoolInfos", methods={"GET"})
     */
    public function getAllSchoolInfos(): JsonResponse
    {
        $schoolInfos = $this->schoolInfoRepository->getAllSchoolInfo();
        $data = [];

        foreach ($schoolInfos as $schoolInfo) {
            $data[] = [
                'id' => $schoolInfo->getId(),
                'name' => $schoolInfo->getName(),
                'phone' => $schoolInfo->getPhone(),
                'email' => $schoolInfo->getEmail(),
                'address' => $schoolInfo->getAddress(),
                'province' => $schoolInfo->getProvince(),
                'city' => $schoolInfo->getCity(),
                'subdistrict' => $schoolInfo->getSubdistrict(),
                'postalCode' => $schoolInfo->getPostalCode(),
            ];
        }

        return new JsonResponse(['schoolInfos' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_schoolInfo", methods={"PUT"})
     */
    public function updateSchoolInfo($id, Request $request): JsonResponse
    {
        $schoolInfo = $this->schoolInfoRepository->findOneBy(['id' => $id]);
        $data = (object)json_decode($request->getContent(), true);

        $this->schoolInfoRepository->updateSchoolInfo($schoolInfo, $data);

        return new JsonResponse(['status' => 'schoolInfo updated!'], Response::HTTP_OK);
    }
}
