<?php
namespace Controller\Service;

use Controller\Model\Acumulador;
use Controller\Model\Imposto;
use Controller\Repository\AcumuladorRepositoryInterface;

class AcumuladorService
{
    public function __construct(private AcumuladorRepositoryInterface $repository){}

    public function compareAcumuladores(int $idBase, int $idCompare): array|null
    {
        $dataArrayBase = 
            $this->reduce($this->repository->getAcumuladorPorEmpresa($idBase));

        $dataArrayCompare = 
            $this->reduce($this->repository->getAcumuladorPorEmpresa($idCompare));
        
        
        return $this->getComparisonErros(
            array_values($dataArrayBase), 
            array_values($dataArrayCompare));
            
    }

    public function reduce($dataArray)
    {
        $dataObjects = [];

        foreach($dataArray as $array) {
            if(array_key_exists($array["codAcumulador"], $dataObjects)) {
                $dataObjects[$array["codAcumulador"]]
                    ->addImpostos(new Imposto($array["codImposto"], $array["aliqImposto"]))
                    ->addCfop($array["cfop"])
                    ->addCfps($array["cfps"]);
                continue;
            }

            $dataObjects[$array["codAcumulador"]] = Acumulador::createFromArray($array);
        }

        return $dataObjects;
    }

    public function getComparisonErros(array $acumuladoresBase, array $acumuladoresCompare)
    {
        $errors = [];

        foreach($acumuladoresBase as $acumuladorBase) {
            $acumuladorCompare = array_filter(
                        $acumuladoresCompare, 
                        fn($item) => $acumuladorBase->getCodAcumulador() == $item->getCodAcumulador());

            $acumuladorBase->compare(!empty($acumuladorCompare) ? reset($acumuladorCompare) : null, $errors);
        }

        return $errors;
    }
}