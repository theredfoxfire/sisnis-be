<?php

namespace App\Controller;

use App\Repository\SubjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SubjectSiteController
 * @package App\Controller
 *
 * @Route(path="/api/subject")
 */
class SubjectController
{
    private $subjectRepository;

    public function __construct(SubjectRepository $subjectRepository)
    {
        $this->subjectRepository = $subjectRepository;
    }

    /**
     * @Route("/add", name="add_subject", methods={"POST"})
     */
    public function addSubject(Request $request): JsonResponse
    {
        $data = (object)json_decode($request->getContent(), true);

        if (empty($data->name) || empty($data->serial)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->subjectRepository->saveSubject($data);

        return new JsonResponse(['status' => 'Subject added!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/get/{id}", name="get_one_subject", methods={"GET"})
     */
    public function getOneSubject($id): JsonResponse
    {
        $subject = $this->subjectRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $subject->getId(),
            'name' => $subject->getName(),
            'serial' => $subject->getSerial(),
        ];

        return new JsonResponse(['subject' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_subjects", methods={"GET"})
     */
    public function getAllSubjects(): JsonResponse
    {
        $subjects = $this->subjectRepository->findAll();
        $data = [];

        foreach ($subjects as $subject) {
            $data[] = [
                'id' => $subject->getId(),
                'name' => $subject->getName(),
                'serial' => $subject->getSerial(),
            ];
        }

        return new JsonResponse(['subjects' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_subject", methods={"PUT"})
     */
    public function updateSubject($id, Request $request): JsonResponse
    {
        $subject = $this->subjectRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        $this->subjectRepository->updateSubject($subject, $data);

        return new JsonResponse(['status' => 'subject updated!']);
    }

    /**
     * @Route("/delete/{id}", name="delete_subject", methods={"DELETE"})
     */
    public function deleteSubject($id): JsonResponse
    {
        $subject = $this->subjectRepository->findOneBy(['id' => $id]);

        $this->subjectRepository->removeSubject($subject);

        return new JsonResponse(['status' => 'subject deleted']);
    }
}
