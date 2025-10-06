<?php
namespace Controller\Model;

class Empresa
{
    private ?string $codEmpresa;
    private ?string $apelido;
    private ?string $razaoSocial;
    private ?string $cnpj;

    public static function createFromArray(array $data): Empresa
    {
        $newObject = new Empresa();

        $newObject
            ->setCodEmpresa(str_pad($data['codEmpresa'], 6, " ", STR_PAD_LEFT))
            ->setApelido($data['apelido'])
            ->setRazaoSocial($data['razaoSocial'])
            ->setCnpj($data['cnpj'])
            ->formatCnpj();

        return $newObject;
    }

    public function __toString(): string
    {
        return "{$this->codEmpresa} | {$this->cnpj} | {$this->apelido}";
    }

    private function formatCnpj(): void
    {
        $this->cnpj = 
            substr($this->cnpj, 0, 2) . 
            "." . substr($this->cnpj, 2, 3) . 
            "." . substr($this->cnpj, 5, 3) .
            "/" . substr($this->cnpj, 8, 4) .
            "-" . substr($this->cnpj, 12, 2);
    }

    public function getCodEmpresa(): ?string
    {
        return $this->codEmpresa;
    }

    public function setCodEmpresa(?string $codEmpresa): self
    {
        $this->codEmpresa = $codEmpresa;

        return $this;
    }

    public function getApelido(): ?string
    {
        return $this->apelido;
    }

    public function setApelido(?string $apelido): self
    {
        $this->apelido = $apelido;

        return $this;
    }

    public function getRazaoSocial(): ?string
    {
        return $this->razaoSocial;
    }

    public function setRazaoSocial(?string $razaoSocial): self
    {
        $this->razaoSocial = $razaoSocial;

        return $this;
    }

    public function getCnpj(): ?string
    {
        return $this->cnpj;
    }

    public function setCnpj(?string $cnpj): self
    {
        $this->cnpj = $cnpj;

        return $this;
    }
}