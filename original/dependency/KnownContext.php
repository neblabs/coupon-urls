<?php

namespace CouponURLs\Original\Dependency;

use CouponURLs\Original\Characters\StringManager;
use CouponURLs\Original\Dependency\Abilities\Context;

use function CouponURLs\Original\Utilities\Text\i;

/**
 * Not implemented yet, but an idea of an interface to be used to decide if the context
 * for the requested type is correct.
 *
 * This way you can have two or more Dependency classes for the same type
 * but for different contexts, like one Dependency should be used on a
 * Subscriber and another Dependency when the same type is requested by
 * a Controller
 */
class KnownContext implements Context
{
    public function __construct(
        protected StringManager $fullyQualifiedTypeName,
        protected StringManager $methodName,
        protected StringManager $variableName
    ) {}
    
    public function classIs(string $fullyQualifiedClassName) : bool
    {
        return $this->checkTheyAreTheSame(i($fullyQualifiedClassName), $this->fullyQualifiedTypeName);
    }

    // should be used to match interfaces and parent classes
    /*public function isType() : void
    {
        return '';
    }*/

    public function methodIs(string $name) : bool
    {
        return $this->checkTheyAreTheSame(i($name), $this->methodName);
    }

    public function nameIs(string $variableName) : bool
    {
        return $this->checkTheyAreTheSame(i($variableName), $this->variableName);
    }

    protected function checkTheyAreTheSame(StringManager $firstString, StringManager $secondString) : bool
    {
        return $firstString->__is($secondString, caseInsensitive: true);
    }

    public function fullyQualifiedTypeName() : StringManager
    {
        return $this->fullyQualifiedTypeName;
    }

    public function methodName() : StringManager
    {
        return $this->methodName;
    }

    public function name() : StringManager
    {
        return $this->variableName;
    }
    
    //public function isConstructor() : bool;
    //public function isConstructorOfClass(string $fullyQualifiedClassName) : bool;
}