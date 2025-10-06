<?php
namespace Controller\Model;

use JsonSerializable;

class Imposto implements JsonSerializable
{
    public function __construct(private ?int $codImposto = null, private ?float $aliqImposto = null){}

    public function jsonSerialize(): array
    {
        return [
            'codImposto' => $this->codImposto,
            'aliqImposto' => $this->aliqImposto
        ];
    }

    public function getCodImposto(): ?int
    {
        return $this->codImposto;
    }

    public function setCodImposto(?int $codImposto): self
    {
        $this->codImposto = $codImposto;

        return $this;
    }

    public function getAliqImposto(): ?float
    {
        return $this->aliqImposto;
    }

    public function setAliqImposto(?float $aliqImposto): self
    {
        $this->aliqImposto = $aliqImposto;

        return $this;
    }

    public function __toString(): string
    {
        return "( código {$this->codImposto} [" . number_format($this->aliqImposto, 2) . "%] )";
    }
}