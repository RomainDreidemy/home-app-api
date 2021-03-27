<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public function __construct(public UserPasswordEncoderInterface $passwordEncoder)
    {}

    public function load(ObjectManager $manager)
    {
        $users = [
            ['name' => 'Romain Dreidemy', 'email' =>'dreidemyromain@gmail.com', 'password' => 'Romain'],
            ['name' => 'Lucie Pompougnac', 'email' =>'lucie.pompougnac@gmail.com', 'password' => 'Lucie'],
            ['name' => 'testFindActiveForUser', 'email' =>'testFindActiveForUser@gmail.com', 'password' => 'testFindActiveForUser'],
        ];


        foreach ($users as $userI){
            $user = (new User())
                ->setName($userI['name'])
                ->setEmail($userI['email'])
            ;
            $user->setPassword($this->passwordEncoder->encodePassword($user, $userI['password']));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
