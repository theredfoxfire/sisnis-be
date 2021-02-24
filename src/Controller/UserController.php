<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserSiteController
 * @package App\Controller
 *
 * @Route(path="/api/user")
 */
class UserController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/get/{id}", name="get_one_user", methods={"GET"})
     */
    public function getOneUser($id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
        ];

        return new JsonResponse(['user' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all/{role}", name="get_all_users", methods={"GET"})
     */
    public function getAllUsers($role): JsonResponse
    {
        $users = $this->userRepository->getAllUser($role);
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'username' => $user->getUsername(),
                'isActive' => $user->getIsActive(),
            ];
        }

        return new JsonResponse(['users' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_user", methods={"PUT"})
     */
    public function updateUser($id, Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $data = (object) json_decode($request->getContent(), true);

        $this->userRepository->updateUser($user, $data, $encoder);

        return new JsonResponse(['status' => 'user updated!'], Response::HTTP_OK);
    }
    /**
     * @Route("/update/{id}/activate", name="update_activate_user", methods={"PUT"})
     */
    public function activateUser($id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $this->userRepository->activateUser($user);

        return new JsonResponse(['status' => 'user updated!'], Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", name="delete_user", methods={"DELETE"})
     */
    public function deleteUser($id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        $this->userRepository->removeUser($user);

        return new JsonResponse(['status' => 'user deleted'], Response::HTTP_OK);
    }
}
