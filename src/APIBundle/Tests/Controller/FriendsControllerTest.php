<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 27.11.15
 * Time: 22:35
 */

namespace APIBundle\Tests\Controller;

use APIBundle\Document\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Class FriendsControllerTest
 * @package APIBundle\Tests\Controller
 */
class FriendsControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    protected $data;

    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @var User[]
     */
    protected $users = [];

    protected $time;

    /**
     * FriendsControllerTest constructor.
     */
    public function __construct()
    {
        $this->data = json_decode(file_get_contents(__DIR__.'/data/friends.json'));
        parent::__construct();
    }

    /**
     * setUp() method runs before each method of the test.
     */
    public function setUp()
    {
        $this->client = static::createClient();
        $this->documentManager = $this->client
            ->getContainer()
            ->get('doctrine_mongodb')
            ->getManager();

        $this->data = json_decode(file_get_contents(__DIR__.'/data/friends.json'));
        foreach ($this->data as $doc) {
            $user = new User();
            $user->setFriends($doc->friends)
                ->setName($doc->name)
                ->setId(new \MongoId($doc->_id->str))
                ->setApikey($doc->apikey);
            if (property_exists($doc, 'friendshipRequests')) {
                $user->setFriendshipRequests($doc->friendshipRequests);
            }
            $this->documentManager->persist($user);
            $this->users[] = $user;
        };
        $this->documentManager->flush();

        parent::setUp();
    }

    /**
     * TEST [GET] /friends
     */
    public function testGetFriends()
    {
        $this->timeStart();
        $this->client->request(
            'GET',
            '/friends/list',
            [],
            [],
            [
                "HTTP_apikey" => "8818762d847f2dd47c85fdcee1824cd2",
            ]
        );
        $this->timeEnd('get friends');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent())->data;
        $this->assertCount(14, $content);
        $this->assertEquals('565c1f0c21d6c45bcf27c7e0', $content[0]->id);
        $this->assertEquals('Cyril Alington', $content[0]->name);
        $this->assertEquals('565c1f0c21d6c45bcf27c83d', $content[13]->id);
        $this->assertEquals('John Berger', $content[13]->name);
    }

    /**
     * TEST [GET] /friends/requests
     */
    public function testGetFriendshipRequests()
    {
        $this->timeStart();
        $this->client->request(
            'GET',
            '/friends/requests',
            [],
            [],
            [
                "HTTP_apikey" => "f3b006f6cbc86cd1af64ccd1faddeda3",
            ]
        );
        $this->timeEnd('get friends');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(
            "{\"status\":\"success\",\"data\":"
            . "[{\"id\":\"565c1f0c21d6c45bcf27c7ef\",\"name\":\"Moniza Alvi\"},"
            . "{\"id\":\"565c1f0c21d6c45bcf27c80a\",\"name\":\"Arthur John Arberry\"}]}",
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * TEST [POST] /friends
     */
    public function testUnsupportedMethod()
    {
        $this->client->request('POST', '/friends/list');

        $this->assertEquals(405, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test adding a friend when the friend did not apply a friendship request
     */
    public function testAddFriendshipRequest()
    {
        $collection = $this->getCollection();
        $user = $collection->findOne(['_id' => new \MongoId("565c1f0c21d6c45bcf27c7ee")]);
        $this->assertEquals([
            "565c1f0c21d6c45bcf27c827",
            "565c1f0c21d6c45bcf27c82a",
        ], $user['friendshipRequests']);
        $this->timeStart();
        $this->client->request(
            'PUT',
            '/friends/addfriend',
            //the user that is not in friendshipRequests must be added to friendshipRequests
            ["friendId" => "565c1f0c21d6c45bcf27c7ee"],
            [],
            [
                "HTTP_apikey" => "915ff7487e35507586c9c5fd75b9c5e6",
            ]
        );
        $this->timeEnd('add friendship request');
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("{\"status\":\"success\",\"data\":[]}", $this->client->getResponse()->getContent());

        $userUpdated = $collection->findOne(['_id' => new \MongoId("565c1f0c21d6c45bcf27c7ee")]);
        $this->assertEquals([
            "565c1f0c21d6c45bcf27c827",
            "565c1f0c21d6c45bcf27c82a",
            "565c1f0c21d6c45bcf27c831",
        ], $userUpdated['friendshipRequests']);
    }

    /**
     * Test adding a friend when the friend sent a friendship request
     */
    public function testPostAddFriend()
    {
        $collection = $this->getCollection();
        $user = $collection->findOne(['_id' => new \MongoId("565c1f0c21d6c45bcf27c7ee")]);
        $this->assertEquals([
            "565c1f0c21d6c45bcf27c827",
            "565c1f0c21d6c45bcf27c82a",
        ], $user['friendshipRequests']);

        $this->assertNotContains("565c1f0c21d6c45bcf27c827", $user['friends']);

        $friend = $collection->findOne(['_id' => new \MongoId("565c1f0c21d6c45bcf27c827")]);
        $this->assertNotContains("565c1f0c21d6c45bcf27c7ee", $friend['friends']);
        $this->timeStart();
        $this->client->request(
            'PUT',
            '/friends/addfriend',
            //the user from friendshipRequests must be added to friends
            ["friendId" => "565c1f0c21d6c45bcf27c7ee"],
            [],
            [
                "HTTP_apikey" => "3e696adf581c3f14144b949e41afa504",
            ]
        );
        $this->timeEnd('add friend');

        $userUpdated = $collection->findOne(['_id' => new \MongoId("565c1f0c21d6c45bcf27c7ee")]);

        $this->assertEquals([
            "565c1f0c21d6c45bcf27c82a",
        ], $userUpdated['friendshipRequests']);

        $this->assertEquals([
            '565c1f0c21d6c45bcf27c82e',
            '565c1f0c21d6c45bcf27c828',
            '565c1f0c21d6c45bcf27c81d',
            '565c1f0c21d6c45bcf27c80f',
            '565c1f0c21d6c45bcf27c7fb',
            '565c1f0c21d6c45bcf27c82c',
            '565c1f0c21d6c45bcf27c825',
            '565c1f0c21d6c45bcf27c820',
            '565c1f0c21d6c45bcf27c824',
            '565c1f0c21d6c45bcf27c7db',
            '565c1f0c21d6c45bcf27c80e',
            '565c1f0c21d6c45bcf27c7e1',
            '565c1f0c21d6c45bcf27c7df',
            "565c1f0c21d6c45bcf27c827",
        ], $userUpdated['friends']);

        $friendUpdated = $collection->findOne(['_id' => new \MongoId("565c1f0c21d6c45bcf27c827")]);
        $this->assertContains("565c1f0c21d6c45bcf27c7ee", $friendUpdated['friends']);
    }

    /**
     * TEST [PUT] /friends/request/accept
     */
    public function testAcceptFriendshipRequest()
    {
        $collection = $this->getCollection();
        $user = $collection->findOne(['apikey' => "f3b006f6cbc86cd1af64ccd1faddeda3"]);
        $this->assertEquals([
            "565c1f0c21d6c45bcf27c7ef",
            "565c1f0c21d6c45bcf27c80a",
        ], $user['friendshipRequests']);
        $this->assertCount(25, $user['friends']);
        $this->assertNotContains("565c1f0c21d6c45bcf27c80a", $user['friends']);
        $this->timeStart();
        $this->client->request(
            'PUT',
            '/friends/request/accept',
            ["friendId" => "565c1f0c21d6c45bcf27c80a"],
            [],
            [
                "HTTP_apikey" => "f3b006f6cbc86cd1af64ccd1faddeda3",
            ]
        );
        $this->timeEnd("accept friendship");
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("{\"status\":\"success\",\"data\":[]}", $this->client->getResponse()->getContent());

        $user = $collection->findOne(['apikey' => "f3b006f6cbc86cd1af64ccd1faddeda3"]);
        $this->assertEquals([
            "565c1f0c21d6c45bcf27c7ef",
        ], $user['friendshipRequests']);
        $this->assertCount(26, $user['friends']);
        $this->assertContains("565c1f0c21d6c45bcf27c80a", $user['friends']);
    }

    /**
     * TEST [DELETE] /friends/request/decline
     */
    public function testDeclineFriendshipRequest()
    {
        $collection = $this->getCollection();
        $user = $collection->findOne(['apikey' => "f3b006f6cbc86cd1af64ccd1faddeda3"]);
        $this->assertEquals([
            "565c1f0c21d6c45bcf27c7ef",
            "565c1f0c21d6c45bcf27c80a",
        ], $user['friendshipRequests']);
        $this->assertCount(25, $user['friends']);
        $this->assertNotContains("565c1f0c21d6c45bcf27c80a", $user['friends']);
        $this->timeStart();
        $this->client->request(
            'DELETE',
            '/friends/request/decline',
            ["userId" => "565c1f0c21d6c45bcf27c80a"],
            [],
            [
                "HTTP_apikey" => "f3b006f6cbc86cd1af64ccd1faddeda3",
            ]
        );
        $this->timeEnd("decline friendship");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("{\"status\":\"success\",\"data\":[]}", $this->client->getResponse()->getContent());
        $user = $collection->findOne(['apikey' => "f3b006f6cbc86cd1af64ccd1faddeda3"]);
        $this->assertEquals([
            "565c1f0c21d6c45bcf27c7ef",
        ], $user['friendshipRequests']);
        $this->assertCount(25, $user['friends']);
        $this->assertNotContains("565c1f0c21d6c45bcf27c80a", $user['friends']);
    }

    /**
     * Test get friends of friends with depth=0
     */
    public function testGetFriendsOfFriendsZeroDepth()
    {
        $this->timeStart();
        do {
            $this->client->request(
                'GET',
                '/friends/friendsoffriends?depth=0',
                [],
                [],
                [
                    "HTTP_apikey" => "01370c75d384a033ef9d8b5ed384c04c",
                ]
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['data'];
            sleep(1);
        } while ($data['progress'] < 100);
        $this->timeEnd("FoF zero");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent())->data->friends;
        $this->assertCount(9, $content);
        $this->assertEquals("565c1f0c21d6c45bcf27c7f0", $content[0]->id);
        $this->assertEquals("Eric Ambler", $content[0]->name);
        $this->assertEquals("565c1f0c21d6c45bcf27c80b", $content[1]->id);
        $this->assertEquals("Harriet Arbuthnot", $content[1]->name);
        $this->assertEquals("565c1f0c21d6c45bcf27c815", $content[2]->id);
        $this->assertEquals("Robert Armin", $content[2]->name);
        $this->assertEquals("565c1f0c21d6c45bcf27c817", $content[3]->id);
        $this->assertEquals("Martin Armstrong", $content[3]->name);
        $this->assertEquals("565c1f0c21d6c45bcf27c85b", $content[4]->id);
        $this->assertEquals("Hester Biddle", $content[4]->name);
        $this->assertEquals("565c1f0c21d6c45bcf27c864", $content[5]->id);
        $this->assertEquals("Isabella Bird", $content[5]->name);
        $this->assertEquals("565c1f0c21d6c45bcf27c865", $content[6]->id);
        $this->assertEquals("Dea Birkett", $content[6]->name);
        $this->assertEquals("565c1f0c21d6c45bcf27c867", $content[7]->id);
        $this->assertEquals("Samuel Bishop", $content[7]->name);
        $this->assertEquals("565c1f0c21d6c45bcf27c869", $content[8]->id);
        $this->assertEquals("Robert Black", $content[8]->name);
    }

    /**
     * Test get friends of friends with depth=1
     */
    public function testGetFriendsOneDepth()
    {
        $this->timeStart();
        do {
            $this->client->request(
                'GET',
                '/friends/friendsoffriends?depth=1',
                [],
                [],
                [
                    "HTTP_apikey" => "01370c75d384a033ef9d8b5ed384c04c",
                ]
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['data'];
            sleep(1);
        } while ($data['progress'] < 100);
        $this->timeEnd('FoF one');
        $content = json_decode($this->client->getResponse()->getContent())->data->friends;
        $this->assertCount(128, $content);
        $this->assertEquals("565c1f0c21d6c45bcf27c7d3", $content[0]->id);
        $this->assertEquals("Mary Hayley Bell", $content[0]->name);
        $this->assertEquals("565c1f0c21d6c45bcf27c86f", $content[127]->id);
        $this->assertEquals("Lorna Doone", $content[127]->name);
    }

    /**
     * Test get friends of friends with depth=1000
     */
    public function testGetFriendsThousandDepth()
    {
        $this->timeStart();
        do {

            $this->client->request(
                'GET',
                '/friends/friendsoffriends?depth=1000',
                [],
                [],
                [
                    "HTTP_apikey" => "01370c75d384a033ef9d8b5ed384c04c",
                ]
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['data'];
            sleep(1);
        } while ($data['progress'] < 100);
        $this->timeEnd('FoF 1000');
        $this->assertCount(157, $data['friends']);
    }

    protected function getCollection()
    {
        $mongo = new \MongoClient();
        $db = $mongo->selectDB("testfriends");
        $collection = $db->selectCollection('User');
        $collection->createIndex(['apikey' => 1], ['unique' => true]);

        return $collection;
    }

    /**
     * Test destructor
     */
    public function tearDown()
    {
        foreach ($this->users as $user) {
            $this->documentManager->remove($user);
        };
        $this->documentManager->flush();

        parent::tearDown();
    }

    protected function timeStart()
    {
        $this->time = microtime(true);
    }

    protected function timeEnd($label)
    {
        $time = microtime(true) - $this->time;
        var_dump($label.':'.$time);
    }
}
