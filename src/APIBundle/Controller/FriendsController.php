<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 27.11.15
 * Time: 22:58
 */

namespace APIBundle\Controller;

use APIBundle\Document\FofCache;
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
    public function getRequestsAction(Request $request)
    {
        $apiKey = $request->headers->get('apikey');
        $friendshipRequests = $this->container->get('doctrine_mongodb')
            ->getRepository('APIBundle:User')
            ->getFriendshipRequests($apiKey);

        return new APIResponse($friendshipRequests);
    }

    /**
     * @param Request $request
     * @return APIResponse
     */
    public function getFriendsoffriendsAction(Request $request)
    {
        $apiKey = $request->headers->get('apikey');
        $depth = (int) $request->query->get('depth');

        $friendsOfFriends = $this->container->get('doctrine_mongodb')
            ->getRepository('APIBundle:FofCache')
            ->getFriendsOfFriends($apiKey, $depth);

        if (!$friendsOfFriends) {
            $msg = array(
                'apiKey' =>  $apiKey,
                'depth' => $depth,
            );
            $this->get('old_sound_rabbit_mq.friends_of_friends_producer')->publish(serialize($msg));

            return new APIResponse(new FofCache());
        }

        return new APIResponse($friendsOfFriends);
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

    /**
     * @param Request $request
     * @return APIResponse
     */
    public function putRequestAcceptAction(Request $request)
    {
        $apiKey = $request->headers->get('apikey');
        $friendId = $request->request->get('friendId');
        $this->container->get('doctrine_mongodb')
            ->getRepository('APIBundle:User')
            ->acceptFriendshipRequest($apiKey, $friendId);

        return new APIResponse([], 201);
    }

    /**
     * @param Request $request
     * @return APIResponse
     */
    public function deleteRequestDeclineAction(Request $request)
    {
        $apiKey = $request->headers->get('apikey');
        $userId = $request->request->get('userId');
        $this->container->get('doctrine_mongodb')
            ->getRepository('APIBundle:User')
            ->declineFriendshipRequest($apiKey, $userId);

        return new APIResponse([], 200);
    }
}
