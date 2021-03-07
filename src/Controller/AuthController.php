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
        $parameters = json_decode($request->getContent(), true);

        $password = $parameters['password'];
        $email = $parameters['email'];
        $name = $parameters['name'];


        $errors = [
            'Tous les champs doivent être remplis'                  => $this->authService->isEmptyField($email, $name, $password),
            'L\'email n\'est pas valide'                            => !filter_var($email, FILTER_VALIDATE_EMAIL),
            'Le mot de passe doit contenir au moins 5 caractères'   => strlen($password) < 5,
            'L\'email est déjà utilisé.'                            => $this->authService->emailIsUsed($email),
        ];

        if(in_array(true, $errors)){
            return $this->json([
                'status' => false,
                'message' => array_search(true, $errors)
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
        $parameters = json_decode($request->getContent(), true);

        $password = $parameters['password'];
        $email = $parameters['email'];

        $user = $userRepository->findOneBy([
            'email'=> $email,
        ]);

        if (!$user || !$encoder->isPasswordValid($user, $password)) {
            return $this->json([
                'status' => false,
                'message' => 'email ou mot de passe incorrect.',
            ]);
        }

        $payload = [
            "user" => $user->getUsername(),
            "exp"  => (new \DateTime())->modify("+1 month")->getTimestamp(),
        ];

        $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'), 'HS256');

        return $this->json([
            'status' => true,
            'token' => sprintf('Bearer %s', $jwt),
        ]);
    }
}
