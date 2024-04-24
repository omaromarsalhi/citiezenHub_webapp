<?php

namespace App\MyHelpers;

class AiDataHolder
{
    private array $descriptions = [];
    private array $titleValidation = [];
    private array $categoryValidation = [];

    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    public function setDescriptions(array $descriptions): self
    {
        $this->descriptions = $descriptions;
        return $this;
    }

    public function getTitleValidation(): array
    {
        return $this->titleValidation;
    }

    public function setTitleValidation(array $titleValidation): self
    {
        $this->titleValidation = $titleValidation;
        return $this;
    }

    public function getCategoryValidation(): array
    {
        return $this->categoryValidation;
    }

    public function setCategoryValidation(array $categoryValidation): self
    {
        $this->categoryValidation = $categoryValidation;
        return $this;
    }

}