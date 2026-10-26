<?php

namespace CouponURLs\Original\Validation\Validators;

use CouponURLs\Original\Abilities\Methods;
use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Exceptions\InvalidImplementationException;
use CouponURLs\Original\Validation\Exceptions\ValidationException;
use CouponURLs\Original\Validation\PassingValidationResult;
use CouponURLs\Original\Validation\ValidationResult;
use CouponURLs\Original\Validation\Validator;
use Exception;
use ReflectionAttribute;
use ReflectionClass;
use function CouponURLs\Original\Utilities\Collection\_;
use function CouponURLs\Original\Utilities\Text\i;

Class DuckMethodsAreImplementedCorrectly extends Validator
{

    protected ReflectionClass $duckInterfaceReflection;
    protected ReflectionClass $implementationReflection;

    public function __construct(
        protected string $interface,
        protected string $implementation
    ) {
        $this->duckInterfaceReflection = new ReflectionClass($interface);
        $this->implementationReflection = new ReflectionClass($implementation);
    }

    public function execute() : ValidationResult
    {
        //   if (version_compare(PHP_VERSION, '8.0.0', '<')) {
            return new PassingValidationResult();
       // }
        
        foreach ($this->interfaceMethods()->names() as $methodName) {
            if (!$this->implementationReflection->hasMethod($methodName)) {
                return $this->failed()->withException(
                    new InvalidImplementationException("Missing required method: {$this->implementationReflection->getShortName()}::{$methodName}")
                );
            }
        }

        return $this->passWhen($this->interfaceMethods()->names()->haveAny());
    }

    protected function interfaceMethods() : Methods
    {
        return _($this->duckInterfaceReflection->getAttributes())->find(
            fn(ReflectionAttribute $reflectionAttribute) => 
                i($reflectionAttribute->getName())->toLowerCase()
                                                ->__is(
                                                    i(Methods::class)->toLowerCase()
                                                )
        )->newInstance();
    }
    
    protected function getDefaultException() : Exception
    {
        return new InvalidImplementationException(
            "Class: {$this->implementationReflection->getName()} must correctly implement interface {$this->duckInterfaceReflection->getName()}"
        );
    }
}