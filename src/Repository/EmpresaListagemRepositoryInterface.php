<?php
namespace Controller\Repository;

interface EmpresaListagemRepositoryInterface
{
    public function getListaEmpresas(callable | null $filter, bool | null $details): array;
    public function getListaEmpresasECF(string $year): array;
}