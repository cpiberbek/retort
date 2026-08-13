<?php

namespace App\Traits;

trait HasActivePlant
{
    public function getPlantAttribute($value)
    {
        return $this->plant_active ?: $value;
    }
}