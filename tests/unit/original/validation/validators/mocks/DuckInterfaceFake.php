<?php

namespace CouponURLs\Tests\Unit\Original\Validation\Validators\Mocks;

use CouponURLs\Original\Abilities\DuckInterface;
use CouponURLs\Original\Abilities\Methods;

/**
 * @method noArguments arguments(string $name, array $posts) returns(void)
 * @method onlyReturnTypeScalar arguments(string $name, array $posts) returns(bool)
 * @method onlyReturnTypeObject arguments(string $name, array $posts) returns(CouponURLs\Original\Collections\Collection)
 * @method scalarArguments arguments(string $name, array $posts) returns(void)
 * @method typeArguments arguments(\stdClass $standardClass, CouponURLs\Original\Collections\Collection $aCustomClass) returns(CouponURLs\Original\Validation\Validators)
 */
#[DuckInterface]
#[Methods(['create'])]
interface DuckInterfaceFake
{
    
}