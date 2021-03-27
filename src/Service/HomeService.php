<?php


namespace App\Service;


use App\Entity\Home;
use App\Entity\User;
use App\Utils\ErrorHelper;
use App\Utils\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HomeService
{

    public function __construct(private EntityManagerInterface $manager, private ValidatorInterface $validator, private Utils $utils)
    {}

    public function create(string $name, ?User $user): ErrorHelper
    {
        if(is_null($user)){
            return new ErrorHelper(status: false, message: 'L\'utilisateur n\'existe pas.');
        }

        $home = (new Home())
            ->setName($name)
            ->setState(true)
            ->addUser($user)
        ;

        $errors = $this->validator->validate($home);

        if(count($errors) > 0){
            return new ErrorHelper(status: false, message: $errors->get(0)->getMessage());
        }

        $this->manager->persist($home);
        $this->manager->flush();

        return new ErrorHelper(status: true, message: 'La nouvelle maison a été ajoutée', data: $home);
    }

    public function remove(Home $home): Home
    {
        $home->setState(false);

        $this->manager->persist($home);
        $this->manager->flush();

        return $home;
    }

    public function generateShareCode(Home $home): Home
    {
        $share_code_expiration = (new \DateTime())->modify('+3 hour');

        do{
            $share_code = $this->utils->generateRandomString(8);
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