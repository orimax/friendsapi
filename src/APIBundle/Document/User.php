<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 28.11.15
 * Time: 12:05
 */

namespace APIBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use MongoId;

/**
 * @MongoDB\Document(repositoryClass="APIBundle\Repository\UserRepository")
 */
class User
{

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $name;

    /**
     * @MongoDB\Collection
     */
    protected $friends;

    /**
     * @MongoDB\String
     */
    protected $apikey;

    /**
     * @MongoDB\Collection
     */
    protected $friendshipRequests;

    /**
     * Get id
     *
     * @return MongoId $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return self $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set friends
     *
     * @param array $friends
     * @return self $this
     */
    public function setFriends($friends)
    {
        $this->friends = $friends;

        return $this;
    }

    /**
     * Get friends
     *
     * @return array $friends
     */
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * @return array
     */
    public function getFriendshipRequests()
    {
        return (array) $this->friendshipRequests;
    }

    /**
     * @param array $friendshipRequests
     * @return self $this
     */
    public function setFriendshipRequests($friendshipRequests)
    {
        $this->friendshipRequests = $friendshipRequests;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getApikey()
    {
        return $this->apikey;
    }

    /**
     * @param mixed $apikey
     * @return self $this
     */
    public function setApikey($apikey)
    {
        $this->apikey = $apikey;

        return $this;
    }



    /**
     * The function needed to pass the User object to PreAuthenticatedToken object
     *
     * @return string
     */
    public function __toString()
    {
        return "";
    }
}
