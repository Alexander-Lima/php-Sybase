<?php
namespace Controller\Repository;

interface EmpresaListagemRepositoryInterface
{
    public function getListaEmpresas(callable | null $filter, bool | null $observation): array;
    public function getListaEmpresasECF(string $year): array;
}