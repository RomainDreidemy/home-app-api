<?php


namespace App\Service;


use App\Entity\ShoppingItem;
use App\Entity\ShoppingList;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ShoppingItemService
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    public function create(string $name, int $shopping_list_id, int $user_id)
    {
        $shopping_list = $this->manager->getRepository(ShoppingList::class)->find($shopping_list_id);
        $user = $this->manager->getRepository(User::class)->find($user_id);

        $errors = [
            'Liste de course inexistante.' => is_null($shopping_list),
            'Utilisateur inexistant.' => is_null($user),
            'Le nom doit contenir au moins 2 caractères' => strlen($name) < 2
        ];

        if(in_array(true, $errors)){
            return [false, array_search(true, $errors)];
        }

        $shopping_item = (new ShoppingItem())
            ->setName($name)
            ->setBuy(false)
            ->setShoppingList($shopping_list)
            ->setUser($user)
        ;

        $this->manager->persist($shopping_item);
        $this->manager->flush();

        return [true, 'L\'élément à bien été ajouté à la liste de course.'];
    }

    public function switchBuy(int $id)
    {
        /** @var ShoppingItem $shopping_item */
        $shopping_item = $this->manager->getRepository(ShoppingItem::class)->find($id);

        $errors = [
            'Cet item est inexistant.' => is_null($shopping_item),
        ];

        if(in_array(true, $errors)){
            return [false, array_search(true, $errors)];
        }

        $shopping_item->setBuy(!$shopping_item->getBuy());

        $this->manager->persist($shopping_item);
        $this->manager->flush();

        return [true, $shopping_item->getName() . ' a été modifié.'];
    }

    public function remove(int $id)
    {
        /** @var ShoppingItem $shopping_item */
        $shopping_item = $this->manager->getRepository(ShoppingItem::class)->find($id);

        $errors = [
            'Cet item est inexistant.' => is_null($shopping_item),
        ];

        if(in_array(true, $errors)){
            return [false, array_search(true, $errors)];
        }

        $this->manager->remove($shopping_item);
        $this->manager->flush();

        return [true, $shopping_item->getName() . ' a été supprimé.'];
    }
}