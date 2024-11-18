<?php

namespace Dnw\Foundation\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Tests\TestCase;

/**
 * @implements Rule<Class_>
 *
 * @codeCoverageIgnore
 */
class AllowLaravelTestAttributeRule implements Rule
{
    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        // Check if the class extends Tests\TestCase
        if (! $node->extends) {
            return [];
        }

        $extendedClassName = (string) $node->extends->toString();
        if ($extendedClassName !== TestCase::class) {
            return [];
        }

        // Check for the #[AllowLaravelTest] attribute
        $hasAllowLaravelTestAttribute = false;
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($attr->name->toString() === AllowLaravelTestCase::class) {
                    $hasAllowLaravelTestAttribute = true;
                    break 2; // Exit both foreach loops
                }
            }
        }

        // If the attribute is not found, return an error
        if (! $hasAllowLaravelTestAttribute) {
            return [
                RuleErrorBuilder::message('Classes extending Tests\TestCase must have the #[AllowLaravelTest] attribute.')->identifier('foo')->build(),
            ];
        }

        return [];
    }
}
