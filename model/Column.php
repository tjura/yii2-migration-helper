<?php

namespace tjura\migration\model;

class Column
{
    public function __construct(protected string $name, protected array $types, protected ?string $fk)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function setTypes(array $types): self
    {
        $this->types = $types;

        return $this;
    }

    public function getFk(): ?string
    {
        return $this->fk;
    }

    public function setFk(?string $fk): self
    {
        $this->fk = $fk;

        return $this;
    }

}