<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 27.11.15
 * Time: 22:35
 */

namespace APIBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class FriendsControllerTest
 * @package APIBundle\Tests\Controller
 */
class FriendsControllerTest extends WebTestCase
{

    public function testGetFriends()
    {
        $client = static::createClient();
        $client->request('GET', '/friends?apikey=hQDjbfNh457dJakeydEq');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("{\"status\":\"success\",\"data\":[{\"id\":\"5658c8d8e1fcd94fe19e5bb3\",\"name\":\"Patrik Ziuskind\"},{\"id\":\"565975cbe1fcd94fe19e5bb4\",\"name\":\"Alan Dapre\"},{\"id\":\"56597601e1fcd94fe19e5bb5\",\"name\":\"Ronald Cavaye\"},{\"id\":\"56597637e1fcd94fe19e5bb6\",\"name\":\"Gary M Dobbs\"}]}", $client->getResponse()->getContent());
    }

    public function testUnsupportedMethod(){
        $client = static::createClient();
        $client->request('POST', '/friends?apikey=hQDjbfNh457dJakeydEq');

        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }
}
