<?php

namespace Arbustofr;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class ResponseEvent extends Event
{
    private $request;
    private $response;

    /**
     * __construct
     *
     * @param  Response $response
     * @param  Request $request
     * @return void
     */
    public function __construct(Response $response, Request $request)
    {
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * getResponse
     *
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * getRequest
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
