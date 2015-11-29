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
     * @param array $responseArray
     */
    public function __construct($responseArray)
    {
        $response = [
            "status" => "success",
            "data" => $responseArray,
        ];

        parent::__construct($response);
    }
}