<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 28.11.15
 * Time: 20:29
 */

namespace APIBundle\Repository;

use APIBundle\Document\FofCache;
use APIBundle\Exception\FriendshipRequestDoesNotExistException;
use Doctrine\ODM\MongoDB\DocumentRepository;
use APIBundle\Document\User;

/**
 * Class UserRepository
 * @package APIBundle\Repository
 */
class UserRepository extends DocumentRepository
{
    /**
     * @param string $apiKey
     * @return array
     */
    public function getFriends($apiKey)
    {
        $user = $this->findOneBy(["apikey" => $apiKey]);

        $friends = $this->createQueryBuilder()
            ->field("id")
            ->in($user->getFriends())
            ->getQuery()
            ->execute();

        $friendsList = [];

        /** @var User[] $friends */
        foreach ($friends as $friend) {
            $friendsList[] = [
                "id" => (string) $friend->getId(),
                "name" => $friend->getName(),
            ];
        }

        return $friendsList;
    }

    /**
     * @param string $apiKey
     * @param int    $depth
     */
    public function writeFriendsOfFriendsListToCache($apiKey, $depth)
    {
        $cached = $this->getDocumentManager()
            ->getRepository('APIBundle:FofCache')
            ->findOneBy(['apikey' => $apiKey, 'depth' => $depth]);

        if ($cached) {
            return;
        }

        $fofCache = $this->initializeFriendCache($apiKey, $depth);
        /** @var FofCacheRepository $fofRepo */
        $fofRepo =  $this->getDocumentManager()->getRepository('APIBundle:FofCache');
        $user = $this->findOneBy(["apikey" => $apiKey]);
        $friendsOfFriends = $user->getFriends();
        $found = [];
        for ($i = 0; $i < $depth; $i++) {
            $friendsOfFriends = $this->getFriendsOfNextLevel($friendsOfFriends, $found);
            $fofRepo->updateProgress($fofCache->getId(), floor($i / $depth * 100));
        }

        $friendsList = $this->getFullFriendsInfo($friendsOfFriends);

        $fofCache->setProgress(100);
        $fofCache->setFriends($friendsList);

        $this->dm->flush();
    }

    /**
     * @param string $apiKey
     * @return array
     */
    public function getFriendshipRequests($apiKey)
    {
        $user = $this->findOneBy(["apikey" => $apiKey]);

        $friends = $this->createQueryBuilder()
            ->field("id")
            ->in($user->getFriendshipRequests())
            ->getQuery()
            ->execute();

        $friendsList = [];

        /** @var User[] $friends */
        foreach ($friends as $friend) {
            $friendsList[] = [
                "id" => (string) $friend->getId(),
                "name" => $friend->getName(),
            ];
        }

        return $friendsList;
    }

    /**
     * If the added user exists in friendshipRequests, the user is added to the friends collection
     * and deleted from friendshipRequests. Otherwise, the user is added to the friendshipRequests.
     *
     * @param string $apiKey   The apiKey of the user, who adds a friend
     * @param string $friendId The ID of the user, that will receive the friendship request
     */
    public function addFriend($apiKey, $friendId)
    {
        /** @var User $user */
        $user = $this->findOneBy(['apikey' => $apiKey]);
        /** @var User $friend */
        $friend = $this->findOneBy(['_id' => new \MongoId($friendId)]);

        if (in_array((string) $user->getId(), $friend->getFriendshipRequests())) {
            $this->addToFriendsList($friendId, (string) $user->getId());
        } else {
            $this->createQueryBuilder()
                ->update()
                ->field('_id')->equals(new \MongoId($friendId))
                ->field('friendshipRequests')->push((string) $user->getId())
                ->getQuery()
                ->execute();
        }
    }

    /**
     * Move user from friendshipRequests to friends.
     *
     * @param string $apiKey
     * @param string $friendId
     * @throws FriendshipRequestDoesNotExistException
     */
    public function acceptFriendshipRequest($apiKey, $friendId)
    {
        /** @var User $user */
        $user = $this->findOneBy(['apikey' => $apiKey]);
        if (!in_array($friendId, $user->getFriendshipRequests())) {
            throw new FriendshipRequestDoesNotExistException();
        }

        $this->addToFriendsList((string) $user->getId(), $friendId);
    }

    /**
     * @param string $apiKey
     * @param string $userId
     */
    public function declineFriendshipRequest($apiKey, $userId)
    {
        /** @var User $user */
        $this->createQueryBuilder()
            ->update()
            ->field('apikey')->equals($apiKey)
            ->field('friendshipRequests')
            ->pull($userId)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $friendId - the users, that owns a friendship request
     * @param string $userId - the user, who applies to be friends
     */
    protected function addToFriendsList($friendId, $userId)
    {
        $this->createQueryBuilder()
            ->update()
            ->field('_id')->equals(new \MongoId($friendId))
            ->field('friends')
            ->push($userId)
            ->field('friendshipRequests')
            ->pull($userId)
            ->getQuery()
            ->execute();
        $this->createQueryBuilder()
            ->update()
            ->field('_id')->equals($userId)
            ->field('friends')
            ->push($friendId)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $apiKey
     * @param int    $depth
     * @return FofCache
     */
    protected function initializeFriendCache($apiKey, $depth)
    {
        $fofCache = new FofCache();
        $fofCache->setDepth($depth);
        $fofCache->setApikey($apiKey);
        $this->dm->persist($fofCache);
        $this->dm->flush();

        return $fofCache;
    }

    /**
     * @param $friendsOfFriends
     * @param $found
     * @return array
     */
    protected function getFriendsOfNextLevel($friendsOfFriends, &$found)
    {
        $toFind = array_diff($friendsOfFriends, $found);
        $friends = $this->createQueryBuilder()
            ->field("id")
            ->in($toFind)
            ->getQuery()
            ->execute();
        $found = array_unique(array_merge($found, $toFind));

        /** @var User[] $friends */
        foreach ($friends as $friend) {
            $friendsOfFriends = array_unique(array_merge($friendsOfFriends, $friend->getFriends()));
        }

        return $friendsOfFriends;
    }

    /**
     * @param $friendsOfFriends
     * @return array
     */
    protected function getFullFriendsInfo($friendsOfFriends)
    {
        $friends = $this->createQueryBuilder()
            ->field("id")
            ->in($friendsOfFriends)
            ->getQuery()
            ->execute();

        $friendsList = [];
        /** @var User[] $friends */
        foreach ($friends as $friend) {
            $friendsList[] = [
                "id" => (string) $friend->getId(),
                "name" => $friend->getName(),
            ];
        }

        return $friendsList;
    }
}
