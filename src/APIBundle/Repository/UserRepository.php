<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 28.11.15
 * Time: 20:29
 */

namespace APIBundle\Repository;

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
                "id" => $friend->getId(),
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
            $this->createQueryBuilder()
                ->update()
                ->field('_id')->equals(new \MongoId($friendId))
                ->field('friends')
                ->push((string) $user->getId())
                ->field('friendshipRequests')
                ->pull((string) $user->getId())
                ->getQuery()
                ->execute();
            $this->createQueryBuilder()
                ->update()
                ->field('_id')->equals($user->getId())
                ->field('friends')
                ->push($friendId)
                ->getQuery()
                ->execute();

        } else {
            $this->createQueryBuilder()
                ->update()
                ->field('_id')->equals(new \MongoId($friendId))
                ->field('friendshipRequests')->push((string) $user->getId())
                ->getQuery()
                ->execute();
        }
    }
}
