<?php

namespace Controller\Repository;

use Controller\Config\Database;

class EmpresaListagemRepository implements EmpresaListagemRepositoryInterface
{
    public function __construct(private Database $database){}

    public function getListaEmpresas(callable | null $filter = null, bool | null $observation = true): array
    {
        $query = 
            "SELECT    
                ' ' + empresas.cgce_emp AS CNPJ,
                empresas.razao_emp AS 'RAZÃO SOCIAL',
                empresas.esta_emp AS 'UF', 
            (CASE 
                WHEN empresas.iest_emp IS NULL THEN '-'  
                ELSE ' ' + empresas.iest_emp
            END) AS 'INSCRIÇÃO ESTADUAL',
            (SELECT 
                municipios.NOME_MUNICIPIO_ACENTUADO_MINUSCULO
        
                FROM bethadba.gemunicipio AS municipios 
        
                WHERE municipios.codigo_municipio = empresas.codigo_municipio) AS MUNICÍPIO,
            (CASE 
                WHEN empresas.imun_emp IS NULL THEN '-'  
                ELSE ' ' + empresas.imun_emp
            END) AS 'INSCRIÇÃO MUNICIPAL', 
            (CASE
                WHEN param_simples.mei = 'S' THEN 'MEI'
                WHEN param_simples.optante = 'S' THEN 'SIMPLES NACIONAL'
                ELSE (SELECT
                (CASE
                    WHEN parametros.RFED_PAR  = 1 THEN 'LUCRO REAL'
                    WHEN parametros.RFED_PAR  = 5 THEN 'LUCRO PRESUMIDO'
                    WHEN parametros.RFED_PAR  = 8 THEN 'IMUNE IRPJ'
                    ELSE '-' 
                    END) 
        
                    FROM bethadba.EFPARAMETRO_VIGENCIA AS parametros WHERE parametros.VIGENCIA_PAR = 
                    (SELECT MAX(param.VIGENCIA_PAR)
                        FROM bethadba.EFPARAMETRO_VIGENCIA AS param
                        WHERE param.CODI_EMP = empresas.codi_emp) AND parametros.CODI_EMP = empresas.codi_emp) 
                END) AS REGIME,
                (CASE
                    WHEN empresas.stat_emp = 'A' THEN 'ATIVA'
                    WHEN empresas.stat_emp = 'I' THEN 'INATIVA'
                    WHEN empresas.stat_emp = 'M' THEN 'ATIVA-SEM MOV.'
                    ELSE 'OUTRO'
                END) AS 'STATUS DOMÍNIO',

                (CASE WHEN empresas.stat_emp = 'I' THEN
                    CASE
                        WHEN empresas.tipoi_emp = 1 THEN 'INATIVA'
                        WHEN empresas.tipoi_emp = 2 THEN 'BAIXADA'
                        WHEN empresas.tipoi_emp = 3 THEN 'TRANSFERIDA'
                        WHEN empresas.tipoi_emp = 4 THEN 'INADIMPLENTE'
                    END 
                END) AS 'TIPO INATIVIDADE',

                DATEFORMAT(empresas.dina_emp, 'DD/MM/YYYY') AS 'DATA INATIVIDADE'"

                . ($observation ? ", empresas.OBS_GERAL AS 'OBS'" : "") .
                
                "FROM bethadba.geempre AS empresas
            
                JOIN bethadba.genatjuridica AS natureza_jur 
            
                ON empresas.njud_emp =  natureza_jur.codigo
            
                JOIN (SELECT 
                    consulta_simples.codigo AS codigo,
                    consulta_simples.opt  AS optante,
                    consulta_simples.mei
                FROM
                    (SELECT table1.CODI_EMP AS codigo, MAX(table1.VIGENCIA_PAR) AS maxdate 
                    FROM bethadba.EFPARAMETRO_VIGENCIA AS table1
                    GROUP BY table1.CODI_EMP) AS maxvigencia
                LEFT JOIN   
                    (SELECT   
                        table2.CODI_EMP AS codigo,
                        table2.VIGENCIA_PAR AS vigencia,
                        table2.SIMPLESN_OPTANTE_PAR AS opt,
                        table2.SIMPLESN_MEI_PAR AS mei
                    FROM bethadba.EFPARAMETRO_VIGENCIA AS table2) AS consulta_simples
                ON maxvigencia.codigo = consulta_simples.codigo
                AND maxvigencia.maxdate = consulta_simples.vigencia) AS param_simples
            
                ON empresas.codi_emp =  param_simples.codigo
                
                WHERE empresas.tins_emp = 1
                    AND empresas.apel_emp NOT LIKE '\_%' ESCAPE '\'  
                        
                ORDER BY empresas.razao_emp";

        $data = $this->database->fetchAssoc($query);

        if($filter) {
            return array_filter($data, $filter);
        }
    
        return $data;
    }

    public function getListaEmpresasECF(string $year): array
    {
        $query = "
            DECLARE @year INTEGER
            SET @year = ?

            SELECT
                max_vigencias.CODI_EMP AS CÓDIGO,
                ' ' + empresas.cgce_emp AS CNPJ,
                (CASE
                    WHEN SUBSTRING(empresas.cgce_emp, 12, 1) = '1' THEN 'M'
                    ELSE 'F'
                END) AS TIPO,
                empresas.razao_emp AS 'RAZÃO SOCIAL',
                DATEFORMAT(max_vigencias.vigencia, 'MM/YYYY') AS VIGÊNCIA,
                DATEFORMAT(empresas.dina_emp, 'DD/MM/YYYY') AS 'DATA INATIVAÇÃO',
                (CASE
                    WHEN empresas.stat_emp = 'A' THEN 'ATIVA'
                    WHEN empresas.stat_emp = 'I' THEN 'INATIVA'
                    WHEN empresas.stat_emp = 'M' THEN 'ATIVA-SEM MOV.'
                    ELSE 'OUTROS'
                END) AS 'STATUS DOMÍNIO',
                (CASE WHEN empresas.stat_emp = 'I' THEN
                    CASE
                        WHEN empresas.tipoi_emp = 1 THEN 'OUTROS'
                        WHEN empresas.tipoi_emp = 2 THEN 'BAIXADA'
                        WHEN empresas.tipoi_emp = 3 THEN 'TRANSFERIDA'
                        WHEN empresas.tipoi_emp = 4 THEN 'INADIMPLENTE'
                    END 
                END) AS 'TIPO INATIVIDADE',
                DATEFORMAT(param_geral.INICIOEFETIVO_PAR, 'DD/MM/YYYY') AS 'INÍCIO FISCAL',
                (CASE
                    WHEN vigenciaS.RFED_PAR = 1 THEN 'LUCRO REAL'
                    WHEN vigenciaS.RFED_PAR = 2 OR vigenciaS.RFED_PAR = 4 THEN 'SIMPLES NACIONAL'
                    WHEN vigenciaS.RFED_PAR = 5 THEN 'LUCRO PRESUMIDO'
                    WHEN vigenciaS.RFED_PAR = 8 THEN 'IMUNE IRPJ'
                    WHEN vigenciaS.RFED_PAR = 9 THEN 'ISENTA IRPJ'
                    ELSE STR(vigenciaS.RFED_PAR)
                END) AS REGIME
            FROM (SELECT 
                    CODI_EMP,
                    MAX(VIGENCIA_PAR) AS vigencia
                FROM bethadba.EFPARAMETRO_VIGENCIA
                WHERE VIGENCIA_PAR <= DATEADD(dd, -1, DATEADD(mm, 1, CAST(STR(@year) + '-12' AS DATE)))
                GROUP BY CODI_EMP) AS max_vigencias

            LEFT JOIN
                (SELECT
                    CODI_EMP,
                    VIGENCIA_PAR, 
                    RFED_PAR
                FROM bethadba.EFPARAMETRO_VIGENCIA) AS vigencias

            ON max_vigencias.CODI_EMP = vigencias.CODI_EMP AND max_vigencias.vigencia = vigencias.VIGENCIA_PAR

            LEFT JOIN
                (SELECT 
                    codi_emp,
                    nome_emp,
                    razao_emp,
                    cgce_emp,
                    tins_emp,
                    stat_emp,
                    tipoi_emp,
                    dina_emp,
                    apel_emp
                FROM bethadba.geempre) AS empresas
            ON empresas.codi_emp = max_vigencias.CODI_EMP

            LEFT JOIN 
                (SELECT
                    CODI_EMP,
                    INICIOEFETIVO_PAR
                FROM bethadba.EFPARAM) AS param_geral
            ON param_geral.CODI_EMP = max_vigencias.CODI_EMP

            WHERE empresas.tins_emp = 1
                AND empresas.apel_emp NOT LIKE '\_%' ESCAPE '\'
                AND (empresas.dina_emp IS NULL OR empresas.dina_emp >= CAST(STR(@year) + '-01' AS DATE))
                AND regime != 'SIMPLES NACIONAL'
                AND tipo = 'M'

            ORDER BY empresas.nome_emp";

        return $this
                ->database
                ->fetchPreparedAssoc($query, [["type"=> "i", "value" => $year]]);
    }
}