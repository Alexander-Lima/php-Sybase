<?php
namespace Controller\Model;

use DateTime;
use JsonSerializable;
use Controller\Model\Imposto;

class Acumulador implements JsonSerializable
{
    public ?int $codEmpresa = null;
    private ?int $codAcumulador = null;
    private ?string $nomeAcumulador = null;
    private ?DateTime $vigenciaAcumulador = null;
    private ?int $contaDeb = null;
    private ?int $contaCred = null;
    private ?int $codHistAcumulador = null;
    private array $impostos = [];
    private array $cfop = [];
    private array $cfps = [];
   
    public static function createFromArray(array $array): object
    {
        $newObject = new Acumulador();

        $newObject->setCodEmpresa($array["codEmpresa"]);
        $newObject->setCodAcumulador($array["codAcumulador"]);
        $newObject->setNomeAcumulador($array["nomeAcumulador"]);
        $newObject->setVigenciaAcumulador($array["vigenciaAcumulador"]);
        $newObject->setContaDeb($array["contaDeb"]);
        $newObject->setContaCred($array["contaCred"]);
        $newObject->setCodHistAcumulador($array["codHistAcumulador"]);
        $newObject->addImpostos(new Imposto($array["codImposto"], $array["aliqImposto"]));
        $newObject->addCfop($array["cfop"]);
        $newObject->addCfps($array["cfps"]);
        
        return $newObject;
    }

    public function jsonSerialize(): array
    {
           return [
            "codEmpresa" => $this->codEmpresa,
            "codAcumulador" => $this->codAcumulador,
            "nomeAcumulador" => $this->nomeAcumulador,
            "vigenciaAcumulador" => $this->vigenciaAcumulador->format("d/m/Y"),
            "contaDeb" => $this->contaDeb,
            "contaCred" => $this->contaCred,
            "codHistAcumulador" => $this->codHistAcumulador,
            "impostos" => $this->impostos,
            "cfop" => $this->cfop,
            "cfps" => $this->cfps
        ];
    }

    public function getCodEmpresa(): ?int
    {
        return $this->codEmpresa;
    }

    public function setCodEmpresa(int $codEmpresa): self
    {
        $this->codEmpresa = $codEmpresa;

        return $this;
    }

    public function getCodAcumulador(): ?int
    {
        return $this->codAcumulador;
    }

    public function setCodAcumulador(int $codAcumulador): self
    {
        $this->codAcumulador = $codAcumulador;

        return $this;
    }

    public function getNomeAcumulador(): ?string
    {
        return $this->nomeAcumulador;
    }

    public function setNomeAcumulador(string $nomeAcumulador): self
    {
        $this->nomeAcumulador = $nomeAcumulador;

        return $this;
    }

    public function getVigenciaAcumulador(): DateTime
    {
        return $this->vigenciaAcumulador;
    }

    public function setVigenciaAcumulador(string $vigenciaAcumulador): self
    {
        $this->vigenciaAcumulador = DateTime::createFromFormat("Y-m-d", $vigenciaAcumulador);

        return $this;
    }

    public function getContaDeb(): ?int
    {
        return $this->contaDeb;
    }

    public function setContaDeb(?int $contaDeb): self
    {
        $this->contaDeb = $contaDeb;

        return $this;
    }

    public function getContaCred(): ?int
    {
        return $this->contaCred;
    }

    public function setContaCred(?int $contaCred): self
    {
        $this->contaCred = $contaCred;

        return $this;
    }

    public function getCodHistAcumulador(): ?int
    {
        return $this->codHistAcumulador;
    }

    public function setCodHistAcumulador(?int $codHistAcumulador): self
    {
        $this->codHistAcumulador = $codHistAcumulador;

        return $this;
    }

    public function getImpostos(): array
    {
        return $this->impostos;
    }

    public function setImpostos(array $impostos): self
    {
        $this->impostos = $impostos;

        return $this;
    }

      public function getCfop(): array
    {
        return $this->cfop;
    }

    public function setCfop(array $cfop): self
    {
        $this->cfop = $cfop;

        return $this;
    }

     public function getCfps(): array
    {
        return $this->cfps;
    }

    public function setCfps(array $cfps): self
    {
        $this->cfps = $cfps;

        return $this;
    }

    public function addImpostos(Imposto $impostos): Acumulador
    {
        if($impostos->getCodImposto() !== null) {
            if(!in_array($impostos, $this->impostos)) {
                $this->impostos[] = $impostos;
            }
        }

        return $this;
    }

    public function addCfop(?string $cfop): Acumulador
    {
        if($cfop !== null && !in_array($cfop, $this->cfop)) {
            $this->cfop[] = $cfop;
        }

        return $this;
    }

    public function addCfps(?string $cfps): Acumulador
    {
         if($cfps !== null && !in_array($cfps, $this->cfps)) {
            $this->cfps[] = $cfps;
        }

        return $this;
    }

    public function compare(?Acumulador $acumuladorCompare, array &$errors)
    {
        if($this == $acumuladorCompare) {
            return;
        }

        $currentErrors = [
            "codAcumulador" => $this->getCodAcumulador(),
            "list" => []
        ];

        if($acumuladorCompare === null) {
            $currentErrors["list"][] = "not found";
            $errors[] = $currentErrors;

            return;
        }

        $classMethods = array_filter(
            get_class_methods($this),
            fn($item) => 
                    str_contains($item, "get") &&
                    !str_contains($item, "CodEmpresa") &&
                    !str_contains($item, "VigenciaAcumulador")
                );
        
        $this->sortArrayFields();
        $acumuladorCompare->sortArrayFields();

        foreach($classMethods as $method) {
            $thisMethodResult = $this->$method();
            $compareClassMethodResult = $acumuladorCompare->$method();

            if($thisMethodResult == $compareClassMethodResult) {
                continue;
            }

            if(is_array($thisMethodResult)) {
                $thisMethodResult = implode(", ",$thisMethodResult);
                $compareClassMethodResult = implode(", ",$compareClassMethodResult);
            }

            $currentErrors["list"][] = 
                [
                    "name" => $this->translate(str_replace('get', '', $method)),
                    "description" =>
                    [
                        ($compareClassMethodResult  == null ? 'vazio' :  $compareClassMethodResult),
                        ($thisMethodResult == null ? "vazio" : $thisMethodResult)
                    ]
                ];
        };

        if(!empty($currentErrors["list"])) {
            $errors[] = $currentErrors;
        }
    }

    public function sortArrayFields(): void
    {
        usort($this->impostos, function($a, $b) {
            if($a->getCodImposto() == $b->getCodImposto()) {
                return 0;
            }
            return $a->getCodImposto() - $b->getCodImposto();
        });

        sort($this->cfop);
        sort($this->cfps);
    }

    private function translate(string $fieldName): string
    {
        $nameMap =
        [
            "NomeAcumulador" => "Acumulador",
            "ContaDeb" => "Conta débito",
            "ContaCred" => "Conta crédito",
            "CodHistAcumulador" => "Histórico contábil"
        ];
        
        return key_exists($fieldName, $nameMap) ? $nameMap[$fieldName] : $fieldName;
    }
}