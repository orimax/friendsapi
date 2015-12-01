<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 29.11.15
 * Time: 16:04
 */

namespace APIBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class APIResponse
 * @package APIBundle\Response
 */
class APIResponse extends JsonResponse
{
    /**
     * APIResponse constructor.
     * @param mixed|null $responseArray
     * @param int        $status
     */
    public function __construct($responseArray, $status = 200)
    {
        $response = [
            "status" => "success",
            "data" => $responseArray,
        ];

        parent::__construct($response, $status);
    }
}
