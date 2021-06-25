<?php

namespace Soap\ExtSoapEngine\Configuration\ClassMap;

use ArrayIterator;
use IteratorAggregate;

final class ClassMapCollection implements IteratorAggregate
{
    /**
     * @var array<string, ClassMapInterface>
     */
    private array $classMaps = [];

    public function __construct(ClassMapInterface ... $classMaps)
    {
        foreach ($classMaps as $classMap) {
            $this->classMaps[$classMap->getWsdlType()] = $classMap;
        }
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->classMaps);
    }
}
