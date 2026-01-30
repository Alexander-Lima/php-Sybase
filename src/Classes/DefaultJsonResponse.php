<?php

namespace Controller\Classes;

use Psr\Http\Message\ResponseInterface as Response;

class DefaultJsonResponse
{
    private function __construct(private Response $response){}
    private array $responseBody = [
        "message" => "",
        "success" => false
    ];
    private int $statusCode = 400;

    public static function create(Response $response) {
        return new DefaultJsonResponse($response);
    }

    public function isSuccessfull(bool $success) {
        $this->responseBody["success"] = $success;

        return $this;
    }

    public function withMessage(string $message) {
        $this->responseBody["message"] = $message;
        
        return $this;
    }

    public function withStatusCode(int $statusCode) {
        $this->statusCode = $statusCode;
        
        return $this;
    }

    public function build(): Response {
        $this->response
            ->getBody()
            ->write(json_encode($this->responseBody));

    return $this->response
        ->withStatus($this->statusCode)
        ->withAddedHeader("Content-Type", "application/json");
    }
}