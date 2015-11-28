<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 28.11.15
 * Time: 16:07
 */

namespace APIBundle\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

/**
 * Class APIUserProvider
 * @package APIBundle\Security
 */
class APIUserProvider implements UserProviderInterface
{

    /**
     * @var ManagerRegistry
     */
    protected $mongodb;


    /**
     * APIUserProvider constructor.
     * @param ManagerRegistry $mongodb
     */
    public function __construct(ManagerRegistry $mongodb)
    {
        $this->mongodb = $mongodb;
    }

    public function loadUserByUsername($username)
    {
        // this is used for storing authentication in the session
        // but this API application is stateless
        throw new UnsupportedUserException();
    }

    /**
     * @param UserInterface $user
     * @return void
     * @throws UnsupportedUserException
     */
    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but this API application is stateless
        throw new UnsupportedUserException();
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'APIBundle\Document\User' === $class;
    }

    /**
     * @param string $apiKey
     * @return mixed
     */
    public function loadUserByAPIKey($apiKey)
    {
        $user = $this->mongodb
            ->getRepository('APIBundle:User')
            ->findOneBy(["apikey" => $apiKey]);

        return $user;
    }

}
