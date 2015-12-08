<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 29.11.15
 * Time: 14:31
 */

namespace APIBundle\Exception;

/**
 * Class UserDoesNotExist
 * @package APIBundle\Exception
 */
class UserDoesNotExistException extends APIException
{
    protected $message = "The user with specified API key does not exist. Authentication denied.";
    protected $statusCode = 401;
    protected $errorCode = 1000;
}
