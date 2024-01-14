<?php

namespace App\OptionsResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductOptionsResolver extends OptionsResolver
{
    public function configureName(bool $isRequired = true): self
    {
        $this->setDefined('name')->setAllowedTypes('name', 'string');

        if ($isRequired) {
            $this->setRequired('name');
        }

        return $this;
    }

    public function configureType(bool $isRequired = true): self
    {
        $this->setDefined('type')->setAllowedTypes('type', 'string');

        if ($isRequired) {
            $this->setRequired('type');
        }

        return $this;
    }

    public function configurePrice(bool $isRequired = true): self
    {
        $this->setDefined('price')->setAllowedTypes('price', 'float');

        if ($isRequired) {
            $this->setRequired('price');
        }

        return $this;
    }
}