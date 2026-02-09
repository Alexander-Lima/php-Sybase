<?php
namespace Controller\Service;

use Controller\Repository\EmpresaListagemRepository;

class EmpresasListagemService
{
    public function __construct(private EmpresaListagemRepository $repository){}

    public function getEmpresas(): array
    {
        return $this->repository->getListaEmpresas();
    }
}