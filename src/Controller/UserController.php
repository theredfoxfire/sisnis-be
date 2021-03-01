<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

/**
 * Class UserSiteController
 * @package App\Controller
 *
 * @Route(path="/api/user")
 */
class UserController
{
    private $userRepository;
    private $tokenStorage;

    public function __construct(UserRepository $userRepository, TokenStorageInterface $tokenStorage)
    {
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/get/{id}", name="get_one_user", methods={"GET"})
     */
    public function getOneUser($id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if (empty($user)) {
            throw new NotFoundHttpException('User not found, param is invalid!');
        }
        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
        ];

        return new JsonResponse(['user' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-user/detail", name="get_user_info", methods={"GET"})
     */
    public function getUserInfo(JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        $token = (object)$JWTManager->decode($this->tokenStorage->getToken());
        $user = $this->userRepository->findOneBy(['username' => $token->username]);
        if (empty($user)) {
            throw new NotFoundHttpException('User not found, param is invalid!');
        }
        $data = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'username' => $user->getUsername(),
                'student' => $user->getStudent() ? $user->getStudent()->toArray() : [],
                'children' => $user->getChildren() ? $user->getChildren()->toArray() : [],
                'teacher' => $user->getTeacher() ? $user->getTeacher()->toArray() : [],
            ];

        return new JsonResponse(['user' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all/{role}", name="get_all_users", methods={"GET"})
     */
    public function getAllUsers(Request $request, $role): JsonResponse
    {
        $currentPage = $request->query->get('page');
        $pageItems = $request->query->get('pageItems');
        $start = ($currentPage - 1) * $pageItems;
        $users = $this->userRepository->getAllUser($role, $start, $pageItems);
        $data = [];

        foreach ($users->data as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'username' => $user->getUsername(),
                'isActive' => $user->getIsActive(),
            ];
        }

        return new JsonResponse(['users' => $data, 'totals' => $users->totals], Response::HTTP_OK);
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
