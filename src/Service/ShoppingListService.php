<?php


namespace App\Service;


use App\Entity\Home;
use App\Entity\ShoppingList;
use App\Utils\ErrorHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ShoppingListService
{
    public function __construct(private EntityManagerInterface $manager, private ValidatorInterface $validator){}

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

    public function update(string $name): ErrorHelper
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

    public function remove(ShoppingList $shoppingList): ErrorHelper
    {
        try {
            $this->manager->remove($shoppingList);
            $this->manager->flush();
            return new ErrorHelper(true, 'La liste de course est supprimé.');
        }catch (\Exception $exception){
            return new ErrorHelper(true, $exception->getMessage());
        }
    }

    public function clone(int $shopping_list_id): ErrorHelper
    {
        /** @var ShoppingList $old */
        $old = $this->manager->getRepository(ShoppingList::class)->find($shopping_list_id);

        $new = clone $old;

        $this->manager->persist($new);
        $this->manager->flush();

        return new ErrorHelper(true, 'La liste de course est clonée.', $new);
    }
}