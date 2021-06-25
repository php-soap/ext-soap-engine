<?php

namespace Soap\ExtSoapEngine\Configuration\ClassMap;

use ArrayIterator;
use IteratorAggregate;

final class ClassMapCollection implements IteratorAggregate
{
    /**
     * @var array<ClassMapInterface>
     */
    protected array $classMaps = [];

    public function __construct(ClassMapInterface ... $classMaps)
    {
        $this->classMaps = $classMaps;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->classMaps);
    }
}
