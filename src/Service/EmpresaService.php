<?php
namespace Controller\Service;

use Controller\Model\Empresa;
use Controller\Repository\EmpresaRepositoryInterface;

class EmpresaService
{
    public function __construct(private EmpresaRepositoryInterface $repository){}

    public function getEmpresas(): array
    {
        return array_map([Empresa::class, 'createFromArray'], $this->repository->getEmpresas());
    }

    public function updateComment(string $id, string $comment): bool
    {
        return $this->repository->updateComment($id, $comment);
    }
}