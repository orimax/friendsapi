<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 27.11.15
 * Time: 22:58
 */

namespace APIBundle\Controller;

use APIBundle\Response\APIResponse;
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
    public function getListAction(Request $request)
    {
        $apiKey = $request->headers->get('apikey');
        $friends = $this->container->get('doctrine_mongodb')
            ->getRepository('APIBundle:User')
            ->getFriends($apiKey);

        return new APIResponse($friends);
    }

    /**
     * @param Request $request
     * @return APIResponse
     */
    public function putAddfriendAction(Request $request)
    {
        $apiKey = $request->headers->get('apikey');
        $friendId = $request->request->get('friendId');
        $this->container->get('doctrine_mongodb')
            ->getRepository('APIBundle:User')
            ->addFriend($apiKey, $friendId);

        return new APIResponse([], 201);
    }
}
