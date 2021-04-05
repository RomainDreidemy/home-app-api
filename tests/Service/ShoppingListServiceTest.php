<?php


namespace App\Tests\Service;


use App\DataFixtures\AppFixtures;
use App\DataFixtures\HomeFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Home;
use App\Entity\ShoppingList;
use App\Entity\User;
use App\Repository\HomeRepository;
use App\Repository\UserRepository;
use App\Service\ShoppingListService;
use App\Utils\ErrorHelper;
use App\Service\HomeService;
use Doctrine\Common\Collections\Collection;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShoppingListServiceTest extends KernelTestCase
{
    use FixturesTrait;

    private function getShoppingList()
    {
        self::bootKernel();
        $user = self::$container->get(UserRepository::class)->findOneBy(['email' => 'dreidemyromain@gmail.com']);
        $home = self::$container->get(HomeService::class)->create('Test intégration', $user)->data;

        return (new ShoppingList())
            ->setName('Test intégration')
            ->setHome($home)
            ->setCreatedAt(new \DateTime('now'))
        ;
    }

    private function getHome(): Home
    {
        self::bootKernel();
        return self::$container->get(HomeRepository::class)->findOneBy(['name' => 'testValidCreateShoppingList']);
    }

    public function testValidCreateShoppingList()
    {
//        $this->loadFixtures([AppFixtures::class]);

//        dump($this->getHome());
//        self::bootKernel();
//        $status = self::$container->get(ShoppingListService::class)->create('Test intégration', $this->getHome()->getId());
//        $this->assertTrue($status->status);
    }
}