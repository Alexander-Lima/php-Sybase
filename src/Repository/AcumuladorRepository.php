<?php

namespace Controller\Repository;

use Controller\Config\Database;

class AcumuladorRepository implements AcumuladorRepositoryInterface
{
    public function __construct(private Database $database){}
    public function getAcumuladorPorEmpresa(int $company): array {
        $date = date('Y-m-d', time());
        $query = 
            "SELECT
                MAX_VIGENCIA.CODI_EMP AS codEmpresa,
                MAX_VIGENCIA.CODI_ACU AS codAcumulador,
                NOME_ACUMULADOR.NOME_ACU AS nomeAcumulador,
                MAX_VIGENCIA.VIGENCIA AS vigenciaAcumulador,
                ACUMULADORES.CDEB AS contaDeb,
                ACUMULADORES.CCRE AS contaCred,
                ACUMULADORES.CHIS AS codHistAcumulador,
                IMPOSTOS.CODI_IMP AS codImposto,
                IMPOSTOS.ALBC_IAC AS aliqImposto,
                CFOP.CODI_NAT AS cfop,
                CFPS.CFPS_ACU AS cfps
            FROM (SELECT 
                    CODI_EMP,
                    CODI_ACU,
                    MAX(VIGENCIA_ACU) AS VIGENCIA
                FROM bethadba.EFACUMULADOR_VIGENCIA
                WHERE 
                    CODI_EMP = ? AND
                    VIGENCIA_ACU <= '{$date}'
                GROUP BY CODI_EMP, CODI_ACU
            ) AS MAX_VIGENCIA,
            LATERAL 
                (SELECT
                    NOME_ACU
                FROM bethadba.EFACUMULADOR
                WHERE 
                    CODI_ACU = MAX_VIGENCIA.CODI_ACU AND
                    CODI_EMP = MAX_VIGENCIA.CODI_EMP
            ) AS NOME_ACUMULADOR,
            LATERAL
                (SELECT 
                    NULLIF(CDEB_ACU, 0) AS CDEB,
                    NULLIF(CCRE_ACU, 0) AS CCRE,
                    NULLIF(CHIS_ACU, 0) AS CHIS
                FROM bethadba.EFACUMULADOR_VIGENCIA
                WHERE 
                    CODI_EMP = MAX_VIGENCIA.CODI_EMP AND
                    CODI_ACU = MAX_VIGENCIA.CODI_ACU AND
                    VIGENCIA_ACU = MAX_VIGENCIA.VIGENCIA) AS ACUMULADORES
            LEFT JOIN LATERAL
                (SELECT
                    CODI_IMP,
                    ALBC_IAC
                FROM bethadba.EFACUMULADOR_VIGENCIA_IMPOSTOS
                WHERE
                    CODI_EMP = MAX_VIGENCIA.CODI_EMP AND
                    CODI_ACU = MAX_VIGENCIA.CODI_ACU AND
                    VIGENCIA_ACU = MAX_VIGENCIA.VIGENCIA) AS IMPOSTOS
                LEFT JOIN LATERAL
                    (SELECT
                        CODI_NAT
                    FROM bethadba.EFACUMULADOR_VIGENCIA_CFOP
                    WHERE
                        CODI_EMP = MAX_VIGENCIA.CODI_EMP AND
                        CODI_ACU = MAX_VIGENCIA.CODI_ACU AND
                        VIGENCIA_ACU = MAX_VIGENCIA.VIGENCIA) AS CFOP
                LEFT JOIN LATERAL
                    (SELECT
                        CFPS_ACU
                    FROM bethadba.EFACUMULADOR_VIGENCIA_CFPS
                    WHERE
                        CODI_EMP = MAX_VIGENCIA.CODI_EMP AND
                        CODI_ACU = MAX_VIGENCIA.CODI_ACU AND
                        VIGENCIA_ACU = MAX_VIGENCIA.VIGENCIA) AS CFPS";

        return $this
                ->database
                ->fetchPreparedAssoc($query, [["type"=> "i", "value" => $company]]);
    }
}