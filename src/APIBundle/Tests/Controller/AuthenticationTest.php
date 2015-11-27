<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 26.11.15
 * Time: 21:21
 */

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthenticationTest extends WebTestCase
{
    public function testAuthenticate(){
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}