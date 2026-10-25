<?php declare(strict_types=1);

use CouponURLs\Original\Events\Wordpress\Exceptions\UnhandledExceptionHandler;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Fakes\ExceptionFake;

it('throws the exception it was given to handle', function() {
    (object) $undehandledExceptionHandler = new UnhandledExceptionHandler;

    try {
        throw new ExceptionFake('this exception should not be handled.');
    } catch(Throwable $exception) {
        $undehandledExceptionHandler->handle($exception);
    }
})->throws(ExceptionFake::class, exceptionMessage: 'this exception should not be handled.');