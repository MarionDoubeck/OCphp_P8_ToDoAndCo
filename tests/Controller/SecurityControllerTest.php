<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users;
use App\Repository\UsersRepository;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private ?object $urlGenerator;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function testDisplayLogin()
    {
        $this->client->request('GET', '/connexion');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testLoginWithBadCredentials(): void
    {
        $crawler = $this->client->request('GET', '/connexion');
        $form = $crawler->selectButton('Connexion')->form([
            'username' => 'userToLog',
            'password' => 'fake'
        ]);
        $this->client->submit($form);
        $this->assertSelectorExists('div.alert.alert-danger');
    }

    public function testLoginSuccessfully(): void
    {
        $crawler = $this->client->request('GET', '/connexion');
        $form = $crawler->selectButton('Connexion')->form([
            'username' => 'userToLog',
            'password' => 'userToLog'
        ]);
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.hide-on-mobile', 'Se déconnecter');
    }

    public function testLogout()
    {
        $this->client->request('GET', '/deconnexion');

        $this->assertSelectorExists('.hero', 'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !');
        
    }

/*     public static function login(KernelBrowser $client, string $as): ?Users
    {
        try {
            $user = static::getContainer()->get(UsersRepository::class)->findOneByUsername($as);
            $client->loginUser($user);
            return $user;
        } catch (\Exception $e) {
        }

        return null;
    } */
}
