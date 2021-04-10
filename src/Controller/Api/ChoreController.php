<?php

namespace App\Controller\Api;

use App\Service\ChoreService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/chore')]
class ChoreController extends AbstractController
{
    public function __construct(
        private ChoreService $choreService
    ){}

    #[Route('/chore', name: 'chore', methods: ['GET'])]
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ChoreController.php',
        ]);
    }

    #[Route('', name: 'chore_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);

        $name = $parameters['name'];
        $points = intval($parameters['points']);
        $home_id = intval($parameters['home_id']);

        $status = $this->choreService->create($name, $points, $home_id);

        return $this->json([
            'status' => $status->status,
            'message' => $status->message
        ]);
    }

    #[Route('/distribute', name: 'chore_distribute', methods: ['POST'])]
    public function distribute(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);

        $home_id = intval($parameters['home_id']);
        $reload = boolval($parameters['reload'] ?? false);

        $status = $this->choreService->distribute($home_id, $reload);

        return $this->json([
            'status' => $status->status,
            'message' => $status->message
        ]);
    }
}
