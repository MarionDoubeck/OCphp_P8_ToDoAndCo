<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{


    /**
     * Tests that the login page is accessible.
     *
     * @return void
     */
    public function testLoginPageIsAccessible()
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');

    }//end testLoginPageIsAccessible()


    /**
     * Tests that the logout page throws a LogicException.
     *
     * @return void
     */
    public function testLogoutPageThrowsLogicException()
    {
        $client = static::createClient();

        $client->request('GET', '/logout');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertRouteSame('app_main');

    }//end testLogoutPageThrowsLogicException()


}//end class
