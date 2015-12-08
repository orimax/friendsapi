<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 29.11.15
 * Time: 14:14
 */

namespace APIBundle\Exception;

/**
 * Class APIKeyNotSpecifiedException
 * @package APIBundle\Exception
 */
class APIKeyNotSpecifiedException extends APIException
{
    protected $message = "You have to specify your valid API key to access the API.";
    protected $statusCode = 401;
    protected $errorCode = 1000;
}
