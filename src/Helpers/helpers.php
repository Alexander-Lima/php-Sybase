<?php
use Psr\Http\Message\ResponseInterface as Response;

function toJson(Response $response): Response {
    return $response->withAddedHeader("Content-Type", "application/json");
}


