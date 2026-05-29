<?php
namespace Controller\Repository;

interface SistemaRepositoryInterface
{
    public function getVersion(): string;
}