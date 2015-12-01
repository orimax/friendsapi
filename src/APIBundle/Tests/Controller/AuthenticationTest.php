<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 26.11.15
 * Time: 21:21
 */

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AuthenticationTest
 * @package AppBundle\Tests\Controller
 */
class AuthenticationTest extends WebTestCase
{
    /**
     * Tests the authentication
     */
    public function testAuthenticateHome()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * Authentication without API key is not allowed: not authorized.
     */
    public function testAuthenticateFriends()
    {
        $client = static::createClient();
        $client->request('GET', '/friends');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent());
        $this->assertEquals("failure", $content->status);
    }

    /**
     * Authentication with an API key of non existent user
     */
    public function testAuthenticateWrongUser()
    {
        $client = static::createClient();
        $client->request('GET', '/?apikey=some_non_existent_key');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent());
        $this->assertEquals("failure", $content->status);
    }
}
