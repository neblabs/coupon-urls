<?php declare(strict_types=1);

use CouponURLs\Original\Events\Wordpress\Abilities\ManuallyHandleExceptions;
use CouponURLs\Original\Events\Wordpress\Exceptions\ManualExceptionHandler;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Fakes\ExceptionFake;

it('dispatches to the ManuallyHandleException and return its value if any', function() {
    /** @var \Mockery\MockInterface&ManuallyHandleExceptions */
    (object) $manuallyHandleException = Mockery::mock(ManuallyHandleExceptions::class);
    (object) $manualExceptionHandler = new ManualExceptionHandler($manuallyHandleException);

    (boolean) $programContinuedExecuting = false;
    (object) $exception = new ExceptionFake('should be handled manually');

    $manuallyHandleException->shouldReceive('onException')->once()->with($exception)->andReturn('I managed to recover (kind of!)');
    try {
        throw $exception;
    } catch(Throwable $exception) {
        $result = $manualExceptionHandler->handle($exception);
        $programContinuedExecuting = true;
    }

    expect($programContinuedExecuting)->toBeTrue();
    expect($result)->toBe('I managed to recover (kind of!)');
});