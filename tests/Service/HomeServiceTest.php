<?php


namespace App\Tests\Service;


use App\DataFixtures\UserFixtures;
use App\Entity\Home;
use App\Entity\User;
use App\Utils\ErrorHelper;
use App\Service\HomeService;
use Doctrine\Common\Collections\Collection;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HomeServiceTest extends KernelTestCase
{
    use FixturesTrait;

    private function createHome(string $name = 'test', string $email = 'dreidemyromain@gmail.com'): ErrorHelper
    {
        self::bootKernel();
        $homeService = self::$container->get('App\Service\HomeService');

        $user = self::$container->get('App\Repository\UserRepository')->findOneBy(['email' => $email]);

        return $homeService->create($name, $user);
    }

    private function removeHome(string $name): Home
    {
        self::bootKernel();
        $home = self::$container->get('App\Repository\HomeRepository')->findOneBy(['name' => $name]);
        return self::$container->get('App\Service\HomeService')->remove($home);
    }


    // Début des tests

    public function testValidCreate(){
        $this->loadFixtures([UserFixtures::class]);
        $this->assertTrue($this->createHome()->status);
    }

    public function testInvalidCreate()
    {
        $this->assertFalse($this->createHome(name: 'A')->status);
        $this->assertFalse($this->createHome(name: 'Ab')->status);

        $this->assertFalse($this->createHome(email: 'unknow')->status);
    }

    public function testRemoveHome()
    {
        $this->createHome('testRemoveHome');
        $home = $this->removeHome('testRemoveHome');
        $this->assertFalse($home->getState());
    }

    public function testGenerateRandomString()
    {
        self::bootKernel();
        $home = $this->createHome('testGenerateRandomString')?->data;
        $home_with_share_code = self::$container->get(HomeService::class)->generateShareCode($home);

        $this->assertNotEmpty($home_with_share_code->getShareCode());
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{8}$/', $home_with_share_code->getShareCode());
    }
}