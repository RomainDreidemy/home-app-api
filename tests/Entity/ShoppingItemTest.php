<?php


namespace App\Tests\Entity;


use App\Entity\Home;
use App\Entity\ShoppingItem;
use App\Entity\ShoppingList;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShoppingItemTest extends KernelTestCase
{
    public function getEntity(): ShoppingItem
    {
        return (new ShoppingItem())
            ->setName('Appartement')
            ->setUser((new User())->setName('Test'))
            ->setBuy(true)
            ->setShoppingList((new ShoppingList())->setName('Test'))
        ;
    }


    public function assertHasErrors(ShoppingItem $shoppingItem, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($shoppingItem);
        $this->assertCount($number, $errors);
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testInvalidEntity()
    {
        $this->assertHasErrors($this->getEntity()->setName('A'), 1);
        $this->assertHasErrors($this->getEntity()->setName(''), 1);

        $this->assertHasErrors($this->getEntity()->setUser(null), 1);

        $this->assertHasErrors($this->getEntity()->setShoppingList(null), 1);
    }
}