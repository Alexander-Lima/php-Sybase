<?php

namespace Controller\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\ErrorHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Throwable;

class ErrorMiddleware implements ErrorHandlerInterface
{
    public function __construct(private ResponseFactory $responseFactory) {}

    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails): ResponseInterface
    {
        error_log("{$exception->getFile()} -> {$exception->getMessage()}");
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(json_encode(['error' => $exception->getMessage()]));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
}

