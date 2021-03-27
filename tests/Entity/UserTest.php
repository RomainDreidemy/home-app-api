<?php


namespace App\Tests\Entity;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function getEntity(): User
    {
        return (new User())
            ->setName('John Doe')
            ->setEmail('john.doe@mail.fr')
            ->setPassword('romain')
        ;
    }

    public function assertHasErrors(User $user, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($user);
        $this->assertCount($number, $errors);
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testInvalidEntity()
    {
        $this->assertHasErrors($this->getEntity()->setName('Ab'), 1);
        $this->assertHasErrors($this->getEntity()->setName('A'), 1);
        $this->assertHasErrors($this->getEntity()->setName(''), 1);

        $this->assertHasErrors($this->getEntity()->setEmail(''), 1);
        $this->assertHasErrors($this->getEntity()->setEmail('John Doe'), 1);
        $this->assertHasErrors($this->getEntity()->setEmail('JohnDoe@mail'), 1);
        $this->assertHasErrors($this->getEntity()->setEmail('JohnDoe@mail.'), 1);

        $this->assertHasErrors($this->getEntity()->setEmail(''), 1);
    }
}