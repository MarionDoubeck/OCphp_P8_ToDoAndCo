<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageIsAccessible()
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    public function testLogoutPageThrowsLogicException()
    {
        $client = static::createClient();

        $client->request('GET', '/logout');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertRouteSame('app_main');
    }
}
