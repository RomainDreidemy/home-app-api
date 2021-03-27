<?php


namespace App\Tests\Entity;


use App\Entity\Home;
use App\Entity\ShoppingList;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShoppingListTest extends KernelTestCase
{
    public function getEntity(): ShoppingList
    {
        return (new ShoppingList())
            ->setName('Appartement')
            ->setCreatedAt(new \DateTime('now'))
            ->setModifiedAt(new \DateTime('+ 1 day'))
            ->setHome((new Home())->setName('Test')->setState(true))
        ;
    }


    public function assertHasErrors(ShoppingList $home, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($home);
        $this->assertCount($number, $errors);
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity());
        $this->assertHasErrors($this->getEntity()->setModifiedAt(null));
    }

    public function testInvalidEntity()
    {
        $this->assertHasErrors($this->getEntity()->setName('A'), 1);
        $this->assertHasErrors($this->getEntity()->setName('Ab'), 1);
        $this->assertHasErrors($this->getEntity()->setName(''), 1);

        $this->assertHasErrors($this->getEntity()->setHome(null), 1);
    }
}