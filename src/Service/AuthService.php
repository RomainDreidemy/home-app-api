<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class AuthService
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function emailIsUsed(string $email): bool
    {
        $user = $this->manager->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);

        return $user !== null;
    }
}