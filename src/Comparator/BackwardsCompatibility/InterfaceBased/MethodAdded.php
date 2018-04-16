<?php

declare(strict_types=1);

namespace Roave\ApiCompare\Comparator\BackwardsCompatibility\InterfaceBased;

use Roave\ApiCompare\Change;
use Roave\ApiCompare\Changes;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionMethod;

final class MethodAdded implements InterfaceBased
{
    public function compare(ReflectionClass $fromInterface, ReflectionClass $toInterface) : Changes
    {
        $fromMethods = $this->methods($fromInterface);
        $toMethods   = $this->methods($toInterface);
        $newMethods  = array_diff_key($toMethods, $fromMethods);

        if (! $newMethods) {
            return Changes::new();
        }

        return Changes::fromArray(array_values(array_map(function (ReflectionMethod $method) use (
            $fromInterface
        ) : Change {
            return Change::added(
                sprintf(
                    'Method %s() was added to interface %s',
                    $method->getName(),
                    $fromInterface->getName()
                ),
                true
            );
        }, $newMethods)));
    }

    /** @return ReflectionMethod[] indexed by lowercase method name */
    private function methods(ReflectionClass $interface) : array
    {
        $methods = $interface->getMethods();

        return array_combine(
            array_map(function (ReflectionMethod $method) : string {
                return strtolower($method->getName());
            }, $methods),
            $methods
        );
    }
}
