<?php

namespace App\Controller\Api;

use App\Entity\Home;
use App\Entity\User;
use App\Service\HomeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class HomeController extends AbstractController
{
    private $homeService;
    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    /**
     * @Route("/api/homes", name="home_api")
     */
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->json([
            'homes' => $this->homeService->findActiveForUser($user)
        ]);
    }

    /**
     * @Route("/api/home/create", name="home_create_api", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $name = $request->get('name');

        $this->homeService->create($name, $user);

        return $this->json([
            'status' => true,
            'message' => 'Nouvelle maison créé.'
        ]);
    }

    /**
     * @Route("/api/home/{id}/remove", name="home_remove_api")
     */
    public function remove(int $id): Response
    {
        $this->homeService->remove($this->getDoctrine()->getRepository(Home::class)->find($id));

        return $this->json([
            'status' => true,
            'message' => 'La maison à bien été supprimé.'
        ]);
    }


}
