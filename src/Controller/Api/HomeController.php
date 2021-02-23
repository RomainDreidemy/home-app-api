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

        $errors = [
            'Le nom doit contenir entre 3 et 30 caractères' => strlen($name) < 3 || strlen($name) > 30,
        ];

        if(in_array(true, $errors)){
            return $this->json([
                'status' => false,
                'message' => array_search(true, $errors)
            ]);
        }

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

    /**
     * @Route("/api/home/{id}/generate-share-code", name="home_generate_share_code_api")
     */
    public function generateShareCode(int $id): Response
    {
        $homeReturn = $this->homeService->generateShareCode($this->getDoctrine()->getRepository(Home::class)->find($id));

        return $this->json([
            'status' => true,
            'message' => 'Code de partage créer',
            'share_code' => $homeReturn->getShareCode()
        ]);
    }

    /**
     * @Route("/api/home/join", name="home_invite_api", methods={"POST"})
     */
    public function join(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $share_code = trim($request->get('code'));

        if(!$this->homeService->joinWithShareCode($share_code, $user)){
            return $this->json([
                'status' => false,
                'message' => 'Impossible de rejoindre la maison, vérifiez le code de partage.',
            ]);
        }

        return $this->json([
            'status' => true,
            'message' => 'Bienvenue dans votre nouvelle maison.',
        ]);
    }


}
