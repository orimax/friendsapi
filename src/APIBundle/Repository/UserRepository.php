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
        foreach ($friends as $friend) {
            /** var User[] $friend */
            $friendsList[] = [
                "id" => $friend->getId(),
                "name" => $friend->getName(),
            ];
        }

        return $friendsList;
    }
}
