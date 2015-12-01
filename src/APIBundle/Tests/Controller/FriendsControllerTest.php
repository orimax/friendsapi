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

    protected $users = [];

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

        $this->client->request(
            'GET',
            '/friends/list',
            [],
            [],
            [
                "HTTP_apikey" => "ee578f3749e49042144965c65d8969d1",
            ]
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(
            "{\"status\":\"success\",\"data\":[{\"id\":\"565b9adb21d6c45bcf27aa7c\",\"name\":\"David Almond\"},{\"id\":\"565b9adb21d6c45bcf27aaa9\",\"name\":\"Richard Armstrong\"},{\"id\":\"565b9adb21d6c45bcf27aab7\",\"name\":\"Alan Bennett\"},{\"id\":\"565b9adb21d6c45bcf27aac5\",\"name\":\"Phyllis Bentley\"},{\"id\":\"565c111c21d6c45bcf27ab00\",\"name\":\"Mary Hayley Bell\"},{\"id\":\"565c111c21d6c45bcf27ab11\",\"name\":\"Walter Allen\"},{\"id\":\"565c111c21d6c45bcf27ab19\",\"name\":\"David Almond\"},{\"id\":\"565c111c21d6c45bcf27ab36\",\"name\":\"Roy Apps\"},{\"id\":\"565c111c21d6c45bcf27ab5a\",\"name\":\"Peter Benson\"},{\"id\":\"565c111c21d6c45bcf27ab5d\",\"name\":\"George Bentham\"},{\"id\":\"565c111c21d6c45bcf27ab82\",\"name\":\"Thomas Betterton\"},{\"id\":\"565c111c21d6c45bcf27ab85\",\"name\":\"L. S. Bevington\"},{\"id\":\"565c111c21d6c45bcf27ab8e\",\"name\":\"T. J. Binyon\"},{\"id\":\"565c111c21d6c45bcf27ac1a\",\"name\":\"Henry Digby Beste\"},{\"id\":\"565c111c21d6c45bcf27ac8e\",\"name\":\"Alan Bennett\"},{\"id\":\"565c111c21d6c45bcf27ac91\",\"name\":\"Edwin Keppel Bennett\"},{\"id\":\"565c111c21d6c45bcf27ac9b\",\"name\":\"Nicolas Bentley\"},{\"id\":\"565c111c21d6c45bcf27acac\",\"name\":\"The Book of Saint Albans\"},{\"id\":\"565c111c21d6c45bcf27acb7\",\"name\":\"Henry Digby Beste\"},{\"id\":\"565c111c21d6c45bcf27accf\",\"name\":\"Clementina Black\"},{\"id\":\"565c111c21d6c45bcf27acd2\",\"name\":\"John Blackburn\"},{\"id\":\"565c111c21d6c45bcf27acd3\",\"name\":\"Thomas Blackburn\"},{\"id\":\"565c111c21d6c45bcf27acd5\",\"name\":\"R. D. Blackmore\"},{\"id\":\"565c111c21d6c45bcf27acd6\",\"name\":\"Lorna Doone\"},{\"id\":\"565c111c21d6c45bcf27acda\",\"name\":\"Alice Albinia\"},{\"id\":\"565c111c21d6c45bcf27acdb\",\"name\":\"Mary Alcock\"},{\"id\":\"565c111c21d6c45bcf27ace4\",\"name\":\"Cyril Alington\"},{\"id\":\"565c111c21d6c45bcf27ace7\",\"name\":\"James Allen\"},{\"id\":\"565c111c21d6c45bcf27acea\",\"name\":\"Albert Campion\"},{\"id\":\"565c111c21d6c45bcf27aced\",\"name\":\"Kenneth Allsop\"},{\"id\":\"565c111c21d6c45bcf27acf6\",\"name\":\"Elizabeth Amherst\"},{\"id\":\"565c112b21d6c45bcf27b0f0\",\"name\":\"G.\"},{\"id\":\"565c112b21d6c45bcf27b2c7\",\"name\":\"G.\"},{\"id\":\"565c112b21d6c45bcf27b5d8\",\"name\":\"G.\"}]}",
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

        $this->client->request(
            'POST',
            '/friends/addfriends',
            //the user that is not in friendshipRequests must be added to friendshipRequests
            ["friendId" => "565c1f0c21d6c45bcf27c7ee"],
            [],
            [
                "HTTP_apikey" => "915ff7487e35507586c9c5fd75b9c5e6",
            ]
        );

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

        $this->client->request(
            'POST',
            '/friends/addfriends',
            //the user from friendshipRequests must be added to friends
            ["friendId" => "565c1f0c21d6c45bcf27c7ee"],
            [],
            [
                "HTTP_apikey" => "3e696adf581c3f14144b949e41afa504",
            ]
        );

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

    protected function getCollection()
    {
        $mongo = new \MongoClient();
        $db = $mongo->selectDB("testfriends");

        return $db->selectCollection('User');
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
}
