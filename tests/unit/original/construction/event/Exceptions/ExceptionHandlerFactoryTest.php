<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Event\Exceptions\ExceptionHandlerFactory;
use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\Abilities\AutomaticallyHandleExceptions;
use CouponURLs\Original\Events\Wordpress\Abilities\ManuallyHandleExceptions;
use CouponURLs\Original\Events\Wordpress\Exceptions\ManualExceptionHandler;
use CouponURLs\Original\Events\Wordpress\Exceptions\SilentExceptionHandler;
use CouponURLs\Original\Events\Wordpress\Exceptions\UnhandledExceptionHandler;

use function CouponURLs\Original\Utilities\Collection\a;

it('creates an UnhandledExceptionHandler when subscriber does not implement ManuallyHandleExceptions or AutomaticallyHandleExceptions', function() {
    /** @var \Mockery\MockInterface&Subscriber */
    (object) $templateFactory = Mockery::mock(Subscriber::class);
    (object) $subscriber = Mockery::mock(Subscriber::class);

    (object) $exceptionHandlerFactory = new ExceptionHandlerFactory;

    expect($exceptionHandlerFactory->create($subscriber))->toBeInstanceOf(UnhandledExceptionHandler::class);
});

it('creates a ManualExceptionHandler with the subscriber when it implements ManuallyHandleExceptions', function() {
    /** @var \Mockery\MockInterface&Subscriber */
    (object) $templateFactory = Mockery::mock(Subscriber::class);
    /** @var \Mockery\MockInterface&Subscriber&ManuallyHandleExceptions */
    (object) $subscriber = Mockery::mock(Subscriber::class, ManuallyHandleExceptions::class);

    (object) $exceptionHandlerFactory = new ExceptionHandlerFactory;

    (object) $handler = $exceptionHandlerFactory->create($subscriber);

    expect($handler)->toBeInstanceOf(ManualExceptionHandler::class)
                    ->toEqual(new ManualExceptionHandler($subscriber));
});

it('creates a SilentExceptionHandler with the subscriber when it implements AutomaticallyHandleExceptions', function() {
    /** @var \Mockery\MockInterface&Subscriber */
    (object) $templateFactory = Mockery::mock(Subscriber::class);
    /** @var \Mockery\MockInterface&Subscriber&AutomaticallyHandleExceptions */
    (object) $subscriber = Mockery::mock(Subscriber::class, AutomaticallyHandleExceptions::class);

    (object) $exceptionHandlerFactory = new ExceptionHandlerFactory;

    (object) $handler = $exceptionHandlerFactory->create($subscriber);

    expect($handler)->toBeInstanceOf(SilentExceptionHandler::class);
});

//thorws exception if implements both interfaces