<?php
namespace Controller\Service;

use Controller\Model\Empresa;
use Controller\Repository\EmpresaRepositoryInterface;

class EmpresaService
{
    public function __construct(private EmpresaRepositoryInterface $repository){}

    public function getEmpresas(): array
    {
        return array_map(
            fn($item) => Empresa::createFromArray($item), 
            $this->repository->getEmpresas());
    }
}