<?php

namespace Controller\Repository;

use Controller\Config\Database;

class SistemaRepository implements SistemaRepositoryInterface
{
    public function __construct(private Database $database){}

    public function getVersion(): string
    {
        $query = "SELECT vsis_contabil + '-' + RIGHT('0' + CONVERT(VARCHAR, asis_contabil), 2) AS versao FROM bethadba.geprop";
        return ($this->database->fetchAssoc($query))[0]['versao'];
    }
}