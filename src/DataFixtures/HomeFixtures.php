<?php

namespace App\DataFixtures;

use App\Entity\Home;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeFixtures extends Fixture
{
    public function __construct(public UserPasswordEncoderInterface $passwordEncoder)
    {}

    public function load(ObjectManager $manager)
    {
        $homes = [
            ['name' => 'testValidCreateShoppingList', 'state' => true, 'shareCode' => null, 'shareCodeExpiration' => null],
        ];


        foreach ($homes as $homeI){
            $home = (new Home())
                ->setName($homeI['name'])
                ->setState(true)
                ->setShareCode(null)
                ->setShareCodeExpiration(null)
                ->addUser($manager->getRepository(User::class)->findOneBy(['email' => 'dreidemyromain@gmail.com']))
            ;

            $manager->persist($home);
        }

        $manager->flush();
    }
}
