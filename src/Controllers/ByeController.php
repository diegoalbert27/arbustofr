<?php

namespace Arbustofr\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ByeController
{
    /**
     * index
     *
     * @param  mixed $request
     * @return Response
     */
    public function index($name): Response
    {
        $response = new Response("Goodbye {$name}");
        $response->headers->set('Content-type', 'text/plain');

        return $response;
    }
}
