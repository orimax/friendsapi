<?php
namespace APIBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class APIExceptionListener
 * @package APIBundle\EventListener
 */
class APIExceptionListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // do whatever tests you need - in this example I filter by path prefix

        $exception = $event->getException();
        $response = new JsonResponse(["status" => "failure", "data" => $exception], 500);

// HttpExceptionInterface is a special type of exception that
// holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        }

// Send the modified response object to the event
        $event->setResponse($response);
    }
}
