<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 03.12.15
 * Time: 2:02
 */

namespace APIBundle\Exception;

/**
 * Class FriendshipRequestDoesNotExist
 * @package APIBundle\Exception
 */
class FriendshipRequestDoesNotExistException extends APIException
{
    protected $message = 'The specified friendship request does not exist.';
    protected $statusCode = 400;
    protected $errorCode = 1001;
}
