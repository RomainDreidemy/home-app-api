<?php


namespace App\Service;


use App\Entity\Chore;
use App\Entity\Home;
use App\Entity\User;
use App\Repository\ChoreRepository;
use App\Repository\HomeRepository;
use App\Repository\UserRepository;
use App\Utils\ErrorHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChoreService
{
    public function __construct(
        private HomeRepository $homeRepository,
        private ChoreRepository $choreRepository,
        private UserRepository $userRepository,
        private ValidatorInterface $validator,
        private EntityManagerInterface $manager
    ){}

    public function create(string $name, int $point, int $home_id): ErrorHelper
    {
        $home = $this->homeRepository->find($home_id);

        $chore = (new Chore())
            ->setName($name)
            ->setPoint($point)
            ->setHome($home)
        ;

        $errors = $this->validator->validate($chore);

        if(count($errors) >= 1){
            return new ErrorHelper(status: false, message: $errors->get(0)->getMessage(), data: $chore);
        }

        $this->manager->persist($chore);
        $this->manager->flush();

        return new ErrorHelper(status: true, message: 'La tâche ménagère a été créé.', data: $chore);
    }

    public function update(string $name, int $point): ErrorHelper
    {
        $chore = (new Chore())
            ->setName($name)
            ->setPoint($point)
        ;

        $errors = $this->validator->validate($chore);

        if(count($errors) >= 1){
            return new ErrorHelper(status: false, message: $errors->get(0)->getMessage(), data: $chore);
        }

        $this->manager->persist($chore);
        $this->manager->flush();

        return new ErrorHelper(status: true, message: 'La tâche ménagère a été mis à jour.', data: $chore);
    }

    public function distribute(int $home_id, bool $reload = false): ErrorHelper
    {
        $home = $this->homeRepository->find($home_id);
        $users = $home?->getUser();

        if($reload){
            $this->choreRepository->removeUser($home_id);
        }

        $chores = $this->choreRepository->findWithoutUser($home_id);

        $errorMessage = match(true){
            is_null($home)          => 'La maison n\'existe pas.',
            count($users) === 0     => 'Aucun utilisateur dans la maison.',
            count($chores) === 0    => 'Aucune tâches ménagère à traiter.',
            default => true
        };

        if($errorMessage !== true) return new ErrorHelper(status: false, message: $errorMessage);

        /** @var Chore $chore */
        foreach ($chores as $chore){
            $userWithLessPoint = $this->getUserWithLessPoint(home: $home);
            $this->addUserToChore($chore, $userWithLessPoint);
        }

        return new ErrorHelper(status: true, message: 'Les tâches ont été distribué.');
    }

    private function getUserWithLessPoint(Home $home): User
    {
        $users = $this->userRepository->findByHome($home->getId());
        $userWithLessPoint = null;
        $lessPoint = null;

        foreach ($users as $user){
            $points = 0;

            foreach ($user->getChores() as $choreP){
                $points += $choreP->getPoint();
            }

            if(is_null($lessPoint) || $points < $lessPoint){
                $lessPoint = $points;
                $userWithLessPoint = $user;
            }
        }

        return $userWithLessPoint;
    }

    private function addUserToChore(Chore $chore, User $user)
    {
        $user->addChore($chore);
        $this->manager->persist($user);
        $this->manager->flush();
    }
}