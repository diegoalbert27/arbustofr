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
    public function index(Request $request, $name): string
    {
        return "Goodbye {$name}";
    }
}
