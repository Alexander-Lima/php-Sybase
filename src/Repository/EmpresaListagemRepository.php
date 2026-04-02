<?php

namespace Controller\Repository;

use Controller\Config\Database;

class EmpresaListagemRepository implements EmpresaListagemRepositoryInterface
{
    public function __construct(private Database $database){}

    public function getListaEmpresas(): array
    {
        $query = 
            "SELECT    
                ' ' + empresas.cgce_emp AS cnpj,
                empresas.razao_emp as razao_social,
                empresas.esta_emp AS estado, 
            (CASE 
                WHEN empresas.iest_emp IS NULL THEN '-'  
                ELSE ' ' + empresas.iest_emp
            END) AS insc_estadual,
            (SELECT 
                municipios.NOME_MUNICIPIO_ACENTUADO_MINUSCULO
        
                FROM bethadba.gemunicipio AS municipios 
        
                WHERE municipios.codigo_municipio = empresas.codigo_municipio) AS municipio,
            (CASE 
                WHEN empresas.imun_emp IS NULL THEN '-'  
                ELSE ' ' + empresas.imun_emp
            END) AS insc_municipal, 
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
                END) AS regime,
                (CASE
                    WHEN empresas.stat_emp = 'A' THEN 'ATIVA'
                    WHEN empresas.stat_emp = 'I' THEN 'INATIVA'
                    WHEN empresas.stat_emp = 'M' THEN 'ATIVA-SEM MOV.'
                    ELSE 'OUTRO'
                END) AS status_dominio,

                (CASE WHEN empresas.stat_emp = 'I' THEN
                    CASE
                        WHEN empresas.tipoi_emp = 1 THEN 'INATIVA'
                        WHEN empresas.tipoi_emp = 2 THEN 'BAIXADA'
                        WHEN empresas.tipoi_emp = 3 THEN 'TRANSFERIDA'
                        WHEN empresas.tipoi_emp = 4 THEN 'INADIMPLENTE'
                    END 
                END) AS tipo_inatividade,

                DATEFORMAT(empresas.dina_emp, 'DD/MM/YYYY') as data_inatividade
        
                FROM bethadba.geempre AS empresas
            
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
                    AND empresas.codi_emp < 99999
                    AND empresas.codi_emp NOT IN (110, 111, 112, 113, 9999)  
                        
                ORDER BY empresas.razao_emp";

        return $this->database->fetchAssoc($query);
    }
}