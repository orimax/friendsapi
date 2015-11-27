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
    public function testAuthenticate()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
