<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

class ClassWith3DependenciesFake
{
    public function __construct(
        protected ClassWithNoConstrutorFake $classWithNoConstrutorFake,
        protected ClassDependencyWithOneDependencyFake $classDependencyWithOneDependency,
        protected ClassWithAnEmptyConstructorFake $classWithAnEmptyConstructorFake
    ) {}
    
}