<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;

class UserService
{
    public function __construct(private EntityManagerInterface $manager, private UserPasswordEncoderInterface $passwordEncoder){}

    public function create(string $email, string $name, string $password): bool
    {
        try {
            $user = new User();
            $user
                ->setPassword($this->passwordEncoder->encodePassword($user, $password))
                ->setEmail($email)
                ->setName($name)
            ;

            $this->manager->persist($user);
            $this->manager->flush();

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function checkContent(string $email, string $name, string $password)
    {
        return false;
    }
}