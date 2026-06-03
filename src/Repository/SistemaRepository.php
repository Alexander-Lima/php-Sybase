<?php

namespace Controller\Repository;

use Controller\Config\Database;

class SistemaRepository implements SistemaRepositoryInterface
{
    public function __construct(private Database $database){}

    public function getVersion(): string
    {
        $query = "SELECT vsis_contabil, asis_contabil FROM bethadba.geprop";
        $result = ($this->database->fetchAssoc($query))[0];

        return \sprintf(
            "%s-%s",
            $result["vsis_contabil"],
            $result["asis_contabil"] > 10 ?: "0{$result["asis_contabil"]}"
        );
    }
}