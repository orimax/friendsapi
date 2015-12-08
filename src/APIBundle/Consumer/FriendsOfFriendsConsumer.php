<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 08.12.15
 * Time: 0:07
 */

namespace APIBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class FriendsOfFriendsConsumer
 * @package APIBundle\Consumer
 */
class FriendsOfFriendsConsumer implements ConsumerInterface
{

    protected $mongoDB;

    /**
     * FriendsOfFriendsConsumer constructor.
     * @param object $mongoDB
     */
    public function __construct($mongoDB)
    {
        $this->mongoDB = $mongoDB;
    }

    /**
     * @param AMQPMessage $msg
     * @return void
     */
    public function execute(AMQPMessage $msg)
    {
        $data = unserialize($msg->body);
        $repo = $this->mongoDB->getRepository('APIBundle:User');
        $repo->writeFriendsOfFriendsListToCache($data['apiKey'], $data['depth']);
    }
}
