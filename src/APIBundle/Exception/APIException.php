<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 29.11.15
 * Time: 14:06
 */

namespace APIBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class APIException
 * @package APIBundle\Exception
 */
class APIException extends HttpException
{

    protected $statusCode = 500;
    protected $errorCode = 0;

    /**
     * APIException constructor.
     * @param null            $statusCode
     * @param null            $message
     * @param \Exception|null $previous
     * @param array           $headers
     * @param integer         $code
     */
    public function __construct(
        $statusCode = null,
        $message = null,
        \Exception $previous = null,
        array $headers = [],
        $code = 0
    ) {
        $message = json_encode(
            [
                "status" => "failure",
                "data" => $message ?: $this->message,
                "errorCode" => $this->errorCode,
            ]
        );
        $statusCode = $statusCode ?: $this->statusCode;
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
