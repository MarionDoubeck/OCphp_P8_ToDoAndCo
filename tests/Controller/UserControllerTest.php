<?php

namespace App\Tests\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testManageUsers(): void
    {
    // Créez un mock ou utilisez la vraie instance de votre EntityManager
    $entityManager = $this->createMock(EntityManagerInterface::class);

    // Créez un ou plusieurs utilisateurs pour simuler la réponse de la base de données
    $user1 = new Users();
    $user1->setUsername('john_doe');

    $user2 = new Users();
    $user2->setUsername('jane_doe');

    // Simulez la méthode findAll() de votre repository
    $repositoryMock = $this->createMock(UsersRepository::class);
    $repositoryMock->expects($this->once())
        ->method('findAll')
        ->willReturn([$user1, $user2]);

    // Configurez le gestionnaire d'entité pour renvoyer le mock du référentiel
    $entityManager->method('getRepository')->willReturn($repositoryMock);

    // Effectuez la requête GET sur l'URL du contrôleur
    $this->client->request('GET', '/gestion_des_utilisateurs');

    // Vérifiez que la réponse est réussie
    $this->assertResponseIsSuccessful();

    // Vérifiez que la vue contient les utilisateurs rendus
    $this->assertSelectorTextContains('.user-list', 'john_doe');
    $this->assertSelectorTextContains('.user-list', 'jane_doe');
    }

    public function testManageUsers2(): void
    {
        $this->client->request('GET', '/gestion_des_utilisateurs');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);        
        $this->client->followRedirect();
        $this->assertRouteSame('app_login');
    }

    public function testRegister(): void
    {
        $this->client->request('GET', '/gestion_des_utilisateurs/nouvel_utilisateur');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertRouteSame('app_login');
    }

    public function testEditUser(): void
    {
        $user = static::getContainer()->get('doctrine')->getRepository(Users::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, '/gestion_des_utilisateurs/modifier/' . $user->getUsername());
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertRouteSame('app_login');
    }

    public function testDeleteUser(): void
    {
        $user = static::getContainer()->get('doctrine')->getRepository(Users::class)->findOneBy([]);
        $this->client->request(Request::METHOD_GET, '/gestion_des_utilisateurs/supprimer/' . $user->getUsername());
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertRouteSame('app_login');
    }

    // Ajoutez des tests supplémentaires pour la gestion des utilisateurs en fonction du contexte de connexion
    // Enregistrement d'un nouvel utilisateur, modification d'un utilisateur, suppression d'un utilisateur, etc.
    // Assurez-vous de tester différents scénarios possibles en fonction de l'état de connexion.
}
