<?php

namespace App\Controller\Api;

use App\Entity\Home;
use App\Entity\User;
use App\Service\ErrorHelper;
use App\Service\HomeService;
use App\Service\SerializerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/home')]
class HomeController extends AbstractController
{
    private $homeService;
    private $serializer;
    public function __construct(HomeService $homeService, SerializerService $serializer)
    {
        $this->homeService = $homeService;
        $this->serializer = $serializer;
    }

    #[Route('s', name: 'home_api')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $homes = $this->getDoctrine()->getRepository(Home::class)->findActiveHomesForUser($user);

        return $this->json([
            'homes' => $this->serializer->normalize($homes, ['id', 'name'])
        ]);
    }

    #[Route("/create", name:"home_create_api", methods:['POST'])]
    public function create(Request $request): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        $name = $parameters['name'];

        /** @var User $user */
        $user = $this->getUser();

        try {
            /** @var ErrorHelper $status */
            $status = $this->homeService->create($name, $user);

            return $this->json([
                'status' => $status->status,
                'message' => $status->message
            ]);

        }catch (\Exception $exception){
            return $this->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], 500);
        }



    }

    /**
     * @Route("/{id}", name="home_remove_api", methods={"DELETE"})
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
     * @Route("/{id}/generate-share-code", name="home_generate_share_code_api")
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
     * @Route("/join", name="home_invite_api", methods={"POST"})
     */
    public function join(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);

        /** @var User $user */
        $user = $this->getUser();
        $share_code = trim($parameters['code']);

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

    /**
     * @Route("/{id}", name="home_info_api")
     */
    public function infos(int $id): Response
    {
        $home = $this->getDoctrine()->getRepository(Home::class)->find($id);

        return $this->json($this->serializer->normalize($home, ['id', 'name', 'user' => ['id', 'email', 'name']]));
    }


}
