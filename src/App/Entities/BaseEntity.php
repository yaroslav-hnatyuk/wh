<?php

namespace App\Entities;

abstract class BaseEntity
{
    public function setProperties($data = array())
    {
        if (!empty($data) && is_array($data)) {
            $refl = new \ReflectionClass($this);
            foreach ($data as $propertyToSet => $value) {
                if ($refl->hasProperty($propertyToSet)) {
                    $property = $refl->getProperty($propertyToSet);
                    if ($property instanceof \ReflectionProperty) {
                        $property->setValue($this, $value);
                    }
                }
            }
        }
    }

    public function getArray()
    {
        return (array)$this;
    }
}
