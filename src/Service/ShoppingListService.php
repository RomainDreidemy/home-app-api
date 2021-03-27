<?php


namespace App\Service;


use App\Entity\Home;
use App\Entity\ShoppingList;
use App\Utils\ErrorHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ShoppingListService
{
    private $manager;

    public function __construct(EntityManagerInterface $manager, private ValidatorInterface $validator)
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

    public function create(string $name, int $home_id): ErrorHelper
    {
        $home = $this->manager->getRepository(Home::class)->find($home_id);
        if(is_null($home)){
            return new ErrorHelper(status: false, message: 'La maison n\'existe pas');
        }

        $shoppingList = (new ShoppingList())
            ->setName($name)
            ->setCreatedAt(new \DateTime())
            ->setHome($home)
        ;

        $errors = $this->validator->validate($shoppingList);
        if(count($errors) > 0){
            return new ErrorHelper(status: false, message: $errors->get(0)->getMessage());
        }

        $this->manager->persist($shoppingList);
        $this->manager->flush();

        return new ErrorHelper(status: true, message: 'La liste de course à été créée.');
    }

    public function update(string $name)
    {
        $shoppingList = (new ShoppingList())
            ->setName($name)
            ->setModifiedAt(new \DateTime())
        ;

        $errors = $this->validator->validate($shoppingList);
        if(count($errors) > 0){
            return new ErrorHelper(status: false, message: $errors->get(0)->getMessage());
        }

        $this->manager->persist($shoppingList);
        $this->manager->flush();

        return new ErrorHelper(status: true, message: 'La liste de course a été modifié.');
    }

    public function remove(ShoppingList $shoppingList)
    {
        $this->manager->remove($shoppingList);
        $this->manager->flush();
    }

    public function clone(int $shopping_list_id)
    {
        /** @var ShoppingList $old */
        $old = $this->manager->getRepository(ShoppingList::class)->find($shopping_list_id);

        $new = clone $old;

        $this->manager->persist($new);
        $this->manager->flush();
    }
}