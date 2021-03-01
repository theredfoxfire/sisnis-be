<?php
namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Repository\UserRepository;

class AuthController extends ApiController
{
    private $entityManager;
    private $tokenStorage;
    private $userRepository;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->userRepository = $userRepository;
    }

    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $username = $request->get('username');
        $password = $request->get('password');
        $email = $request->get('email');
        $roles = $request->get('roles');

        if (empty($username) || empty($password) || empty($email) || empty($roles)) {
            return $this->respondValidationError("Invalid Username or Password or Email or Roles");
        }
        $data = [
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'roles' => $roles,
        ];

        $this->userRepository->createUser((object) $data, $encoder);

        return $this->respondWithSuccess('User successfully created');
    }

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getTokenUser(Request $request, JWTTokenManagerInterface $JWTManager, UserPasswordEncoderInterface $encoder)
    {
        $isAuthValid = false;
        $request = $this->transformJsonBody($request);
        $username = $request->get('username');
        $password = $request->get('password');
        $user = $this->userRepository->getByUsername($username);
        if ($user) {
            $isAuthValid = $encoder->isPasswordValid($user[0], $password);
        }
        if ($isAuthValid) {
            $token = new UsernamePasswordToken($user[0], null, 'main', $user[0]->getRoles());
            $this->tokenStorage->setToken($token);
        }
        if ($isAuthValid) {
            $this->userRepository->setPassport($user[0]);
            return new JsonResponse(['token' => $JWTManager->create($user[0])], Response::HTTP_OK);
        } else {
            return new JsonResponse(['message' => 'Login failed.'], Response::HTTP_UNAUTHORIZED);
        }
    }
}
