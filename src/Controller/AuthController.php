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

        if (empty($username) || empty($password) || empty($email)) {
            return $this->respondValidationError("Invalid Username or Password or Email");
        }


        $user = new User($username);
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setEmail($email);
        $user->setIsActive(true);
        $user->setUsername($username);
        $em->persist($user);
        $em->flush();
        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getUsername()));
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
        $user = $this->userRepository->findOneBy(["username" => $username, "isActive" => true]);
        if ($user) {
            $isAuthValid = $encoder->isPasswordValid($user, $password);
        }
        if ($isAuthValid) {
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->tokenStorage->setToken($token);
        }
        if ($isAuthValid) {
            return new JsonResponse(['token' => $JWTManager->create($user)], Response::HTTP_OK);
        } else {
            return new JsonResponse(['message' => 'Auth failed.'], Response::HTTP_UNAUTHORIZED);
        }

    }
}
