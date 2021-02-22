<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AuthService;
use App\Service\UserService;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{
    private $authService;
    private $userService;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    /**
     * @Route("/auth/register", name="register", methods={"POST"})
     */
    public function register(Request $request)
    {
        $password = $request->get('password');
        $email = $request->get('email');
        $name = $request->get('name');

        if($this->authService->emailIsUsed($email)){
            return $this->json([
                'status' => false,
                'message' => 'L\'email est déjà utilisé.'
            ]);
        }

        if(!$this->userService->create($email, $name, $password)){
            return $this->json([
                'status' => false,
                'message' => 'Une erreur est survenu, vérifier les informations que vous avez entré.'
            ]);
        }

        return $this->json([
            'status' => true,
        ]);
    }

    /**
     * @Route("/auth/login", name="login", methods={"POST"})
     */
    public function login(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $user = $userRepository->findOneBy([
            'email'=>$request->get('email'),
        ]);
        if (!$user || !$encoder->isPasswordValid($user, $request->get('password'))) {
            return $this->json([
                'message' => 'email or password is wrong.',
            ], 500);
        }
        $payload = [
            "user" => $user->getUsername(),
            "exp"  => (new \DateTime())->modify("+1 month")->getTimestamp(),
        ];


        $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'), 'HS256');
        return $this->json([
            'message' => 'success!',
            'token' => sprintf('Bearer %s', $jwt),
        ]);
    }
}
