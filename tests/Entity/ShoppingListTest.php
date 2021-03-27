<?php


namespace App\Tests\Entity;


use App\Entity\Home;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HomeTest extends KernelTestCase
{
    public function getEntity(): Home
    {
        return (new Home())
            ->setName('Appartement')
            ->setState(true)
        ;
    }

    public function assertHasErrors(Home $home, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($home);
        $this->assertCount($number, $errors);
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testInvalidEntity()
    {
        $this->assertHasErrors($this->getEntity()->setName('A'), 1);
        $this->assertHasErrors($this->getEntity()->setName('Ab'), 1);
    }
}