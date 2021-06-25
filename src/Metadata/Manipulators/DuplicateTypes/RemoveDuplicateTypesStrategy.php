<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine\Metadata\Manipulators\DuplicateTypes;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Soap\ExtSoapEngine\Metadata\Detector\DuplicateTypeNamesDetector;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Manipulators\TypesManipulatorInterface;
use Soap\Engine\Metadata\Model\Type;

final class RemoveDuplicateTypesStrategy implements TypesManipulatorInterface
{
    public function __invoke(TypeCollection $types): TypeCollection
    {
        $duplicateNames = (new DuplicateTypeNamesDetector())($types);

        return $types->filter(static function (Type $type) use ($duplicateNames): bool {
            return !in_array(Normalizer::normalizeClassname($type->getName()), $duplicateNames, true);
        });
    }
}
