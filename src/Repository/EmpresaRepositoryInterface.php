<?php
namespace Controller\Repository;

interface EmpresaRepositoryInterface
{
    public function getEmpresas(): array;
    public function updateComment(string $id, string $comment): bool;
}