<?php

namespace Controller\Repository;

use Controller\Config\Database;

class EmpresaRepository implements EmpresaRepositoryInterface
{
    public function __construct(private Database $database){}

    public function getEmpresas(): array
    {
        $query = 
            "SELECT 
                codi_emp AS codEmpresa,
                apel_emp AS apelido,
                razao_emp AS razaoSocial,
                cgce_emp AS cnpj
            FROM bethadba.geempre
            WHERE tins_emp = 1 AND codi_emp <= 100007 AND stat_emp = 'A'
            ORDER BY codi_emp";

        return $this->database->fetchAssoc($query);
    }
}