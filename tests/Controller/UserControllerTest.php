<?php

namespace App\Tests\Controller;


use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Security\Core\Security;


class UserControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    public function setUp(): void
    {
        /* $this->client = static::createClient();
        $userRepository = static::getContainer()->get(UsersRepository::class);
        $testUser = $userRepository->findOneByUsername('admin');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $this->client->followRedirects(); */
    }
   /* public function testManageUserWhenNotLoggedIn(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/gestion_des_utilisateurs');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertRouteSame('app_login');
        $client->getCrawler();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testManageUserWhenNotAdmin(): void
    {
        $client = static::createClient();
        SecurityControllerTest::login($client, 'userToLog');
        $client->request(Request::METHOD_GET, '/gestion_des_utilisateurs');
        $this->assertResponseStatusCodeSame(403);
    }
*/
/* $client = static::createClient();
SecurityControllerTest::login($client, 'admin');
$user = $client->getContainer()->get('security.token_storage')->getToken()->getUser();
$client->loginUser($user);
dump($user);  */

    public function testManageUserSuccessfully()
    {
        $this->client = static::createClient();
        $userRepository = static::getContainer()->get(UsersRepository::class);
        $testUser = $userRepository->findOneByUsername('admin');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $this->client->followRedirects();
        $container = $this->client->getContainer();
        $authentication = $container->get('security.token_storage')->getToken();
        $user = $authentication->getUser();
        dump($user->getRoles());
        
        $this->client->request(Request::METHOD_GET, '/gestion_des_utilisateurs');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('h1', 'Gestion des utilisateurs');

    } 
/*
    public function testUserCreateSuccessfully()
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/gestion_des_utilisateurs/nouvel_utilisateur');

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_register');
        $this->assertInstanceOf(Form::class, $crawler->selectButton('Ajouter')->form());

        $client->submitForm('Ajouter', [
            'user[username]' => 'newuser',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'newuser@user.user',
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertRouteSame('homepage');
    }

    public function testEditUserWhenNotLoggedIn(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UsersRepository::class)->findOneByUsername('newuser');
        $client->request(Request::METHOD_GET, '/gestion_des_utilisateurs/modifier/' . $user->getUsername());

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertRouteSame('login');
    }

    public function testEditUserSuccessfully(): void
    {
        $client = static::createClient();
        $user = SecurityControllerTest::login($client, 'userToEdit');
        $client->request(Request::METHOD_GET, '/gestion_des_utilisateurs/modifier/' . $user->getUsername());

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('edit_user');
        $this->assertRequestAttributeValueSame('username', $user->getUsername());

        $client->submitForm('Modifier', [
            'user[username]' => 'userEdited',
            'user[password][first]' => 'userToEdit',
            'user[password][second]' => 'userToEdit',
            'user[email]' => 'userToEdit@example.com',
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('user_management');
        $this->assertSelectorExists('div.alert.alert-success');
    }
    */
}