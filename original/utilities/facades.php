<?php

namespace CouponURLs\Original\Utilities;

use CouponURLs\Original\Collections\Validators\ItemsAreOnlyInstancesOf;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Validation\Validator;
use CouponURLs\Original\Validation\Validators\ValidWhen;
use function CouponURLs\Original\Utilities\Collection\_;

/*******************************************************************************
 *
 *      Validation
 * 
 *******************************************************************************/
/**
 * I know what you're thinking. But no, these aren't really shallow. They are a nice sugar that allows us to do something like this:
 *
 * validate(
 *      new ItemsAreOnlyInstancesOf(
 *          items: $dependencies,
 *          allowedTypes: _(Dependency::class)
 *      )
 * )
 * @throws \Exception
 */
function validate(Validator $validator) : void {
    $validator->validate();
}

/**
 * @throws \Exception
 */
function validateThat(bool $isTrue) : void {
    validate(new ValidWhen($isTrue));
}


