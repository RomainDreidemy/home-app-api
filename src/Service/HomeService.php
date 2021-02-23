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

    private function generateRandomString($longueur = 10): string
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longueurMax = strlen($caracteres);
        $chaineAleatoire = '';
        for ($i = 0; $i < $longueur; $i++)
        {
            $chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
        }
        return $chaineAleatoire;
    }

    public function generateShareCode(Home $home): Home
    {
        $share_code_expiration = (new \DateTime())->modify('+3 hour');

        do{
            $share_code = $this->generateRandomString(8);
        }while($this->manager->getRepository(Home::class)->findOneBy(['share_code' => $share_code]) !== null);

        $home
            ->setShareCode($share_code)
            ->setShareCodeExpiration($share_code_expiration)
        ;

        $this->manager->persist($home);
        $this->manager->flush();

        return $home;
    }

    public function joinWithShareCode(string $share_code, User $user): bool
    {
        /** @var Home $home */
        $home = $this->manager->getRepository(Home::class)->findOneBy(['share_code' => $share_code]);

        if(is_null($home)){
            return false;
        }

        $home
            ->addUser($user)
        ;

        $this->manager->persist($home);
        $this->manager->flush();

        return true;
    }
}