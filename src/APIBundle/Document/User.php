<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 28.11.15
 * Time: 12:05
 */

namespace APIBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

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
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
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
     * @param collection $friends
     * @return self
     */
    public function setFriends($friends)
    {
        $this->friends = $friends;

        return $this;
    }

    /**
     * Get friends
     *
     * @return collection $friends
     */
    public function getFriends()
    {
        return $this->friends;
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
