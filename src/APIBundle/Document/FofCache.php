<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 08.12.15
 * Time: 0:50
 */

namespace APIBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use MongoId;

/**
 * @MongoDB\Document(repositoryClass="APIBundle\Repository\FofCacheRepository")
 */
class FofCache implements \JsonSerializable
{

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $apikey;

    /**
     * @MongoDB\Int
     */
    protected $depth = 0;

    /**
     * @MongoDB\Collection
     */
    protected $friends = [];

    /**
     * @MongoDB\Int
     */
    protected $progress = 0;

    /**
     * Get id
     *
     * @return \MongoId $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set apikey
     *
     * @param string $apikey
     * @return self
     */
    public function setApikey($apikey)
    {
        $this->apikey = $apikey;

        return $this;
    }

    /**
     * Get apikey
     *
     * @return string $apikey
     */
    public function getApikey()
    {
        return $this->apikey;
    }

    /**
     * Set depth
     *
     * @param int $depth
     * @return self
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * Get depth
     *
     * @return int $depth
     */
    public function getDepth()
    {
        return $this->depth;
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
     * Set progress
     *
     * @param int $progress
     * @return self
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Get progress
     *
     * @return int $progress
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Returns the array of data, that will be serialized by json_encode.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'progress' => $this->getProgress(),
            'friends' => $this->getFriends(),
        ];
    }
}
