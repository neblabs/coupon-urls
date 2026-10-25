<?php declare(strict_types=1);

use CouponURLs\Original\Events\Wordpress\Exceptions\SilentExceptionHandler;

it('catches the exception and does nothing with it, allowing the profram to continue', function() {
    (object) $silentExceptionHandler = new SilentExceptionHandler;
    (boolean) $programContinuedExecuting = false;

    try {
        throw new SilentExceptionHandler('Shall be ignored!');
    } catch(Throwable $exception) {
        $silentExceptionHandler->handle($exception);
        $programContinuedExecuting = true;
    }

    expect($programContinuedExecuting)->toBeTrue();
});