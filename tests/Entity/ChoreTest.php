<?php


namespace App\Tests\Entity;


use App\Entity\Chore;
use App\Entity\Home;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChoreTest extends KernelTestCase
{
    public function getEntity(): Chore
    {
        return (new Chore())
            ->setName('Test')
            ->setPoint(2)
            ->setHome(new Home())
        ;
    }

    public function assertHasErrors(Chore $chore, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($chore);
        $this->assertCount($number, $errors);
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testInvalidEntity()
    {
        $this->assertHasErrors($this->getEntity()->setName('T'), 1);
        $this->assertHasErrors($this->getEntity()->setName('Te'), 1);

        $this->assertHasErrors($this->getEntity()->setPoint(0), 1);

        $this->assertHasErrors($this->getEntity()->setHome(null), 1);
    }
}