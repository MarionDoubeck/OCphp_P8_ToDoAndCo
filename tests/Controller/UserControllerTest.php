<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;


    /**
     * Test accessing user list page when not logged in.
     *
     * @return void
     */
    public function testUserListAsNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/users');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertRouteSame('app_login');
    }


    /**
     * Test accessing user list page when not an admin.
     *
     * @return void
     */
    public function testUserListAsNotAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('userToLog');
        $client->loginUser($testUser);
        $client->followRedirects();
        $client->request(Request::METHOD_GET, '/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }


    /**
     * Test accessing user list page as an admin.
     *
     * @return void
     */
    public function testUserListAsAdmin()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin');
        $client->loginUser($testUser);
        $client->followRedirects();
        $crawler = $client->request(Request::METHOD_GET, '/users');

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('user_list');
        $this->assertCount(
            count(static::getContainer()->get(UserRepository::class)->findAll()),
            $crawler->filter('table tbody tr')
        );
    }


    /**
     * Test creating a user as an admin.
     *
     * @return void
     */
    public function testUserCreateAsAdmin()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin');
        $client->loginUser($testUser);
        $client->followRedirects();
        $crawler = $client->request(Request::METHOD_GET, '/users/create');

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('user_create');
        $this->assertInstanceOf(Form::class, $crawler->selectButton('Ajouter')->form());

        $form = $crawler->selectButton('Ajouter')->form();
        $randomName = bin2hex(random_bytes(10));
        $form['user_form[username]'] = 'newuser'.$randomName;
        $form['user_form[password][first]'] = 'password';
        $form['user_form[password][second]'] = 'password';
        $form['user_form[email]'] = $randomName.'@user.user';
        $form['user_form[roles]'] = false;
        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    /**
     * Test creating an admin as an admin.
     *
     * @return void
     */
    public function testAdminCreateAsAdmin()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin');
        $client->loginUser($testUser);
        $client->followRedirects();
        $crawler = $client->request(Request::METHOD_GET, '/users/create');

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('user_create');
        $this->assertInstanceOf(Form::class, $crawler->selectButton('Ajouter')->form());

        $form = $crawler->selectButton('Ajouter')->form();
        $randomName = bin2hex(random_bytes(10));
        $form['user_form[username]'] = 'newadmin'.$randomName;
        $form['user_form[password][first]'] = 'password';
        $form['user_form[password][second]'] = 'password';
        $form['user_form[email]'] = $randomName.'@admin.fk';
        $form['user_form[roles]'] = true;
        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    /**
     * Test editing a user when not logged in.
     *
     * @return void
     */
    public function testEditUserAsNotLoggedIn(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneByUsername('userToEdit');
        $client->request(Request::METHOD_GET, '/users/'.$user->getId().'/edit');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertRouteSame('app_login');
    }


    /**
     * Test editing a user when not an admin.
     *
     * @return void
     */
    public function testEditUserAsNotAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('userToLog');
        $client->loginUser($testUser);
        $client->followRedirects();
        $user = static::getContainer()->get(UserRepository::class)->findOneByUsername('userToEdit');
        $client->request(Request::METHOD_GET, '/users/' . $user->getId() . '/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }


    /**
     * Test editing a user as an admin.
     *
     * @return void
     */
    public function testEditUserAsAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userToEdit = $userRepository->findOneByUsername('userToEdit');
        $testUser = $userRepository->findOneByUsername('admin');
        $client->loginUser($testUser);
        $client->followRedirects();
        $crawler =$client->request(Request::METHOD_GET, '/users/'.$userToEdit->getId().'/edit');

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('user_edit');
        $this->assertRequestAttributeValueSame('id', $userToEdit->getId());
        $this->assertInputValueSame('user_form[username]', $userToEdit->getUsername());
        $this->assertInputValueSame('user_form[email]', $userToEdit->getEmail());

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_form[username]' ]= 'modifiedUser';
        $form['user_form[password][first]'] = 'password';
        $form['user_form[password][second]'] = 'password';
        $form['user_form[email]'] = 'modified@user.user';
        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('div.alert.alert-success');

        $client->request(Request::METHOD_GET, '/users/'.$userToEdit->getId().'/edit');
        $form = $crawler->selectButton('Modifier')->form();
        $form['user_form[username]' ]= 'userToEdit';
        $form['user_form[password][first]'] = 'password';
        $form['user_form[password][second]'] = 'password';
        $form['user_form[email]'] = 'toEdit@user.user';
        $client->submit($form);
    }
}