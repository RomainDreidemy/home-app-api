<?php


namespace App\Service;


use App\Entity\Home;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class HomeService
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function findActiveForUser(User $user)
    {
        $homesR = $this->manager->getRepository(Home::class)->findActiveHomesForUser($user);

        $homes = [];

        foreach ($homesR as $home){
            $homes[] = [
                'id' => $home->getId(),
                'name' => $home->getName()
            ];
        }

        return $homes;
    }

    public function create(string $name, User $user): Home
    {
        $home = (new Home())
            ->setName($name)
            ->setState(true)
            ->addUser($user)
        ;

        $this->manager->persist($home);
        $this->manager->flush();

        return $home;
    }

    public function remove(Home $home): Home
    {
        $home->setState(false);

        $this->manager->persist($home);
        $this->manager->flush();

        return $home;
    }
}