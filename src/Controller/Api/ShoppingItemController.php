<?php

namespace App\Controller\Api;

use App\Entity\ShoppingItem;
use App\Entity\ShoppingList;
use App\Service\SerializerService;
use App\Service\ShoppingItemService;
use App\Service\ShoppingListService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShoppingItemController extends AbstractController
{
    private $shoppingItemService;
    private $serializer;

    public function __construct(ShoppingItemService $shoppingItemService, SerializerService $serializer)
    {
        $this->shoppingItemService = $shoppingItemService;
        $this->serializer = $serializer;
    }

    #[Route('/api/shopping-items/{id}', name: 'shopping_item', methods: ['GET'])]
    public function list($id): Response
    {
        $shopping_items = ($this->getDoctrine()->getRepository(ShoppingList::class)->find($id))?->getShoppingItems();

        $shopping_items_notBuy = $this->getDoctrine()->getRepository(ShoppingItem::class)->findByBuyAndList($id, false);

        $shopping_items_buy = $this->getDoctrine()->getRepository(ShoppingItem::class)->findByBuyAndList($id, true);

        if(is_null($shopping_items)){
            return $this->json([
                'status' => false,
                'message' => 'La liste de course que vous recherchez n\'existe pas ou plus.',
            ]);
        }

        return $this->json([
            'status' => true,
            'shopping_items_buy' => $this->serializer->normalize($shopping_items_buy, ['id', 'name', 'buy', 'user' => ['name']]),
            'shopping_items_not_buy' => $this->serializer->normalize($shopping_items_notBuy, ['id', 'name', 'buy', 'user' => ['name']]),
        ]);
    }

    #[Route('/api/shopping-item', name: 'shopping_item_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);

        $name = $parameters['name'];
        $shopping_list_id = $parameters['shopping_list_id'];
        $user = $this->getUser();

        $status = $this->shoppingItemService->create($name, $shopping_list_id, $user->getId());

        return $this->json([
            'status'    => $status[0],
            'message'   => $status[1],
        ]);
    }

    #[Route('/api/shopping-items/{id}/buy', name: 'shopping_item_buy', methods: ['PATCH'])]
    public function buy(int $id): Response
    {
        $status = $this->shoppingItemService->switchBuy($id);

        return $this->json([
            'status'    => $status[0],
            'message'   => $status[1],
        ]);
    }

    #[Route('/api/shopping-item/{id}', name: 'shopping_item_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $status = $this->shoppingItemService->remove($id);

        return $this->json([
            'status'    => $status[0],
            'message'   => $status[1],
        ]);
    }
}
