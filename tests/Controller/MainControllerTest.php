<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class MainControllerTest extends WebTestCase
{

    /**
     * The client used for testing. Initialized as null.
     */
    private KernelBrowser|null $client = null;


    /**
     * Test case for displaying the homepage.
     *
     * @return void
     */
    public function testDisplayHomepage(): void
    {
        $this->client = static::createClient();
        $this->client->request('GET', '/');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !');

    }//end testDisplayHomepage()


}//end class
