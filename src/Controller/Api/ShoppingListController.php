<?php

namespace App\Controller\Api;

use App\Entity\Home;
use App\Entity\ShoppingList;
use App\Service\HomeService;
use App\Service\SerializerService;
use App\Service\ShoppingListService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/shopping')]
class ShoppingListController extends AbstractController
{
    private $shoppingListService;
    private $serializer;

    public function __construct(ShoppingListService $shoppingListService, SerializerService $serializer)
    {
        $this->shoppingListService = $shoppingListService;
        $this->serializer = $serializer;
    }

    #[Route('s', name: 'shopping_item', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $home_id = $request->get('home_id');

        $home = $this->getDoctrine()->getRepository(Home::class)->find($home_id);
        $shoppingsList = $this->getDoctrine()->getRepository(ShoppingList::class)->findByHome($home);

        return $this->json([
            'shoppings' => $this->serializer->normalize($shoppingsList, ['id', 'name', 'createdAt', 'modifiedAt']),
            'status' => true,
        ]);
    }

    #[Route('', name: 'shopping_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);

        $name = $parameters['name'];
        $home_id = $parameters['home_id'];

        $home = $this->getDoctrine()->getRepository(Home::class)->find($home_id);

        $errors = [
            'name non renseigné' => $name === null,
            'home_id non renseigné' => $home_id === null,
            'Maison introuvable' => $home === null,
        ];

        if(in_array(true, $errors)){
            return $this->json([
                'message' => array_search(true, $errors),
                'status' => false,
            ]);
        }

        $this->shoppingListService->create($name, $home);

        return $this->json([
            'message' => 'La liste de course à été créer avec succes',
            'status' => true,
        ]);
    }

    #[Route('/{id}', name: 'shopping_delete', methods: ['DELETE'])]
    public function delete(int $id){
        $shoppingList = $this->getDoctrine()->getRepository(ShoppingList::class)->find($id);

        if(is_null($shoppingList)){
            return $this->json([
                'status' => false,
                'message' => 'Cette liste n\'existe pas.'
            ]);
        }

        $this->shoppingListService->remove($shoppingList);

        return $this->json([
            'status' => true
        ]);
    }

    #[Route('/{id}', name: 'shopping_delete', methods: ['POST'])]
    public function clone(int $id)
    {
        $this->shoppingListService->clone($id);

        return $this->json([
            'status' => true
        ]);
    }


}
