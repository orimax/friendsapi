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
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAuthenticateFriends()
    {
        $client = static::createClient();
        $client->request('GET', '/friends');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertEquals("No API key found.", $client->getResponse()->getContent());
    }

    public function testAuthenticateWrongUser()
    {
        $client = static::createClient();
        $client->request('GET', '/?apikey=some_non_existent_key');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertEquals("The user with specified API key doesn't exist.", $client->getResponse()->getContent());
    }
}
