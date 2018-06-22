<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testFetchToken()
    {
        $client = static::createClient();

        $client->request('POST', '/token', array('team_name' => 'Team India'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}