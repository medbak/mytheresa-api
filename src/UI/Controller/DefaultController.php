<?php

declare(strict_types=1);

namespace App\UI\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function index(): Response
    {
        return new JsonResponse(['message' => 'Welcome to the MyTheresa API.']);
    }
}
