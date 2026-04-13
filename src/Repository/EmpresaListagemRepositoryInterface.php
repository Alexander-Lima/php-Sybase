<?php
namespace Controller\Repository;

interface EmpresaListagemRepositoryInterface
{
    public function getListaEmpresas(): array;
    public function getListaEmpresasECF(string $year): array;
}