<?php
namespace Controller\Repository;

interface AcumuladorRepositoryInterface
{
    public function getAcumuladorPorEmpresa(int $company):array;
}