<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 27.11.15
 * Time: 22:58
 */

namespace APIBundle\Controller;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FriendsController
 * @package APIBundle\Controller
 */
class FriendsController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $apiKey = $request->query->get('apikey');
        $mongo = $this->container->get('doctrine_mongodb');
        $friends = $mongo
            ->getRepository('APIBundle:User')
            ->getFriends($apiKey);

        return new Response(json_encode($friends));
    }
}
