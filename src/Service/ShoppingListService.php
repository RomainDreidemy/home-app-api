<?php


namespace App\Service;


use App\Entity\Home;
use App\Entity\ShoppingList;
use Doctrine\ORM\EntityManagerInterface;

class ShoppingListService
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getAllByUserId(Home $home): array
    {
        $shoppingLists = $this->manager->getRepository(ShoppingList::class)->findByHome($home);
        $shoppingListsReturn = [];

        /** @var ShoppingList $shoppingList */
        foreach ($shoppingLists as $shoppingList) {
            $shoppingListsReturn[] = [
                'id' => $shoppingList->getId(),
                'name' => $shoppingList->getName(),
                'created_at' => $shoppingList->getCreatedAt()->format('d/m/Y'),
                'modified_at' => $shoppingList->getModifiedAt() !== null ? $shoppingList->getModifiedAt()->format('d/m/Y') : null
            ];
        }

        return $shoppingListsReturn;
    }

    public function create(string $name, Home $home)
    {
        $shoppingList = (new ShoppingList())
            ->setName($name)
            ->setCreatedAt(new \DateTime())
            ->setHome($home)
        ;

        $this->manager->persist($shoppingList);
        $this->manager->flush();
    }

    public function update(string $name)
    {
        $shoppingList = (new ShoppingList())
            ->setName($name)
            ->setModifiedAt(new \DateTime())
        ;

        $this->manager->persist($shoppingList);
        $this->manager->flush();
    }

    public function remove(ShoppingList $shoppingList)
    {
        $this->manager->remove($shoppingList);
        $this->manager->flush();
    }
}