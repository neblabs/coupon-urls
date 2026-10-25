<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Events\WordPress;

use ArgumentCountError;
use CouponURLs\App\Creation\Validators\SubscriberValidatorFactory;
use CouponURLs\Original\Construction\Event\Exceptions\ExceptionHandlerFactory;
use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\Abilities\ExceptionHandler;
use CouponURLs\Original\Events\Wordpress\EventArguments;
use CouponURLs\Original\Events\Wordpress\EventHandler;
use CouponURLs\Original\Events\Wordpress\Exceptions\ManualExceptionHandler;
use CouponURLs\Original\Events\Wordpress\Exceptions\SilentExceptionHandler;
use CouponURLs\Original\Events\Wordpress\Exceptions\UnhandledExceptionHandler;
use CouponURLs\Original\Exceptions\IncorrectImplementationException;
use CouponURLs\Original\Exceptions\InvalidImplementationException;
use CouponURLs\Original\Validation\Validator;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Fakes\ExceptionFake;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Fakes\SubscriberThatThrowsExceptionFake;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Fakes\SubscriberThatThrowsExceptionInsideCreateEventArgumentsFake;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Fakes\SubscriberThatThrowsExceptionInsideValidation;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Fakes\SubscriberThatThrowsExceptionOnExecuteAndThatManuallyHandlesTheExceptionAndThrowsAnotherExceptionFake;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Fakes\SubscriberThatThrowsExceptionOnExecuteAndThatManuallyHandlesTheExceptionFake;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Fakes\SubscriberThatThrowsExceptionOnExecuteFake;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Fakes\SubscriberThatThrowsExceptionWhenInvalidArguemntsToCreateEventArgumentsFake;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Mocks\SubscriberMissingCreateEventArgumentsFake;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Mocks\SubscriberProtectedMethodRequiresArgumentsFake;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Mocks\SubscriberRequesting2ExecuteArguments;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Mocks\SubscriberRequestingUnexisitngExecuteArguments;
use CouponURLs\Tests\Unit\Original\Events\Wordpress\Mocks\SubscriberRequiresEventHandlerFake;
use Exception;
use Mockery;
use stdClass;
use Throwable;
use WP_Post;

use function CouponURLs\Original\Utilities\Collection\a;
use function CouponURLs\Original\Utilities\Collection\o;
use function CouponURLs\Tests\mock;

it('throws excetion if the given subscriber is not implemented correctly', function() {
    /** @var \Mockery\MockInterface&Subscriber */
    (object) $subscriber = mock(Subscriber::class);
    /** @var \Mockery\MockInterface&ExceptionHandlerFactory */
    (object) $exceptionHandlerFactory = Mockery::mock(ExceptionHandlerFactory::class);
    (object) $eventHandler = new EventHandler($subscriber, exceptionHandlerFactory: $exceptionHandlerFactory);
})->throws(InvalidImplementationException::class)->skip();

it('does not throw exception if the given subscriber is not implemented correctly and the environment is production', function() {
    /** @var \Mockery\MockInterface&Subscriber */
    (object) $subscriber = mock(Subscriber::class);
    /** @var \Mockery\MockInterface&ExceptionHandlerFactory */
    (object) $exceptionHandlerFactory = Mockery::mock(ExceptionHandlerFactory::class);
    (object) $eventHandler = new EventHandler(
        $subscriber, 
        exceptionHandlerFactory: $exceptionHandlerFactory,
        subscriberValidatorFactory: new SubscriberValidatorFactory(environment: 'production')
    );
})->skip();

it('test_gets_the_requested_arguments_based_on_the_event_arguments_set', function()
{
    /** @var MockObject|Subscriber */
    (object) $subscriber = $this->getMockBuilder(SubscriberRequesting2ExecuteArguments::class)
                                ->onlyMethods(['execute', 'validator'])
                                ->getMock();
    /** @var \Mockery\MockInterface&ExceptionHandlerFactory */
    (object) $exceptionHandlerFactory = Mockery::mock(ExceptionHandlerFactory::class);
    (object) $pasingValidator = $this->createMock(Validator::class);

    (object) $eventHandler = new EventHandler($subscriber, exceptionHandlerFactory: $exceptionHandlerFactory);

    (object) $post = new WP_Post((object) []);
    (integer) $postId = 87;
    (string) $postTitle = 'Hello world!';
    (array) $authors = [98, 786, 97];

    $subscriber->expects($this->once())
               ->method('execute')
               ->with($post, $postTitle);

    $pasingValidator->expects($this->once())
                    ->method('isValid')
                    ->willReturn(true);

    $subscriber->expects($this->once())
               ->method('validator')
               ->with($authors)
               ->willReturn($pasingValidator);

    $eventHandler->handle($post, $postId, $postTitle, $authors);
});

it('test_throws_exception_when_reuqesting_a_parementr_that_s_not_in_the_execute_method and subscriber has no exception handling', function()
{
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Event does not have the requested argument: post');

    (object) $subscriber = $this->getMockBuilder(SubscriberRequestingUnexisitngExecuteArguments::class)
                                ->onlyMethods(['execute'])
                                ->getMock();
    /** @var \Mockery\MockInterface&ExceptionHandlerFactory */
    (object) $exceptionHandlerFactory = Mockery::mock(ExceptionHandlerFactory::class);
    /** @var \Mockery\MockInterface&UnhandledExceptionHandler */
    (object) $unhandledExceptionHandler = Mockery::mock(UnhandledExceptionHandler::class);
    (object) $eventHandler = new EventHandler($subscriber, exceptionHandlerFactory: $exceptionHandlerFactory);

    (object) $post = new WP_Post((object) []);
    (integer) $postId = 87;
    (string) $postTitle = 'Hello world!';
    (array) $authors = [98, 786, 97];

    $receivedException = o(
        exception: null
    );

    $subscriber->expects($this->never())
               ->method('execute');
                 
    $exceptionHandlerFactory->shouldReceive('create')->once()->with($subscriber)->andReturn($unhandledExceptionHandler);
    $unhandledExceptionHandler->shouldReceive('handle')->once()->withArgs(fn(Exception $exception) => ($receivedException->exception = $exception) || true)->andReturnUsing(fn() => throw $receivedException->exception);
    $eventHandler->handle($post, $postId, $postTitle, $authors);
});
// the above but now with handled exceptions should throw nothing

it('test_execute_method_dont_get_called_when_validator_fails', function()
{
    (object) $subscriber = $this->getMockBuilder(SubscriberRequesting2ExecuteArguments::class)
                                ->onlyMethods(['execute', 'validator'])
                                ->getMock();
    (object) $faillingValidator = $this->createMock(Validator::class);
    /** @var \Mockery\MockInterface&ExceptionHandlerFactory */
    (object) $exceptionHandlerFactory = Mockery::mock(ExceptionHandlerFactory::class);
    (object) $eventHandler = new EventHandler(
        $subscriber, 
        exceptionHandlerFactory: $exceptionHandlerFactory
    );

    (object) $post = new WP_Post((object) []);
    (integer) $postId = 87;
    (string) $postTitle = 'Hello world!';
    (array) $authors = [98, 786, 97];

    $subscriber->expects($this->never())
               ->method('execute');

    $faillingValidator->expects($this->once())
                    ->method('isValid')
                    ->willReturn(false);

    $subscriber->expects($this->once())
               ->method('validator')
               ->with($authors)
               ->willReturn($faillingValidator);

    $exceptionHandlerFactory->shouldReceive('create')->never()->withAnyArgs()->globally()->ordered();

    $eventHandler->handle($post, $postId, $postTitle, $authors);
});

it('test that the event handler is correctly set to a subscriber if it implements The SubscriberRequiresEventHandler interface', function() {
    (object) $subscriber = $this->createMock(SubscriberRequiresEventHandlerFake::class);
    /** @var \Mockery\MockInterface&ExceptionHandlerFactory */
    (object) $exceptionHandlerFactory = Mockery::mock(ExceptionHandlerFactory::class);

    $subscriber->expects($this->once())->method('setEventHandler');

    (object) $eventHandler = new EventHandler($subscriber, exceptionHandlerFactory: $exceptionHandlerFactory);

    $eventHandler->handle();
});

it('gives the correct requested arguments for a protected subscriber method', function() : void {
    (object) $subscriber = $this->createMock(SubscriberProtectedMethodRequiresArgumentsFake::class);
    /** @var \Mockery\MockInterface&ExceptionHandlerFactory */
    (object) $exceptionHandlerFactory = Mockery::mock(ExceptionHandlerFactory::class);
    (object) $eventHandler = new EventHandler($subscriber, exceptionHandlerFactory: $exceptionHandlerFactory);
    (object) $eventArguments = Mockery::mock(EventArguments::class);


    $subscriber->expects($this->once())->method('createEventArguments')->willReturn($eventArguments);

    $eventArguments->shouldReceive('hasKey')->with('numberOfPosts')->andReturn(true);
    $eventArguments->shouldReceive('hasKey')->with('name')->andReturn(true);

    $eventArguments->shouldReceive('get')->with('numberOfPosts')->andReturn(7);
    $eventArguments->shouldReceive('get')->with('name')->andReturn('Rafa');

    $eventHandler->handle($subscriber);

    (array) $requestedarguments = $eventHandler->requestedArgumentsFor('myCustomMethod');

    expect($requestedarguments)->toBe([7, 'Rafa']);
});

it('will silently catch the exception generated inside one of the 3 methods of the subscriber, enabling the program to continue executing', function(Subscriber $subscriber, string $exceptionType, string $exceptionMessage) {
    /** @var \Mockery\MockInterface&ExceptionHandlerFactory */
    (object) $exceptionHandlerFactory = Mockery::mock(ExceptionHandlerFactory::class);
    /** @var \Mockery\MockInterface&SilentExceptionHandler */
    (object) $silentExceptionHandler = Mockery::mock(SilentExceptionHandler::class);
    (integer) $firstArgument = 10;
    (object) $secondArgument = o(name: 'Rafael');

    (object) $eventHandler = new EventHandler(
        subscriber: $subscriber,
        exceptionHandlerFactory: $exceptionHandlerFactory
    );

    $exceptionHandlerFactory->shouldReceive('create')->once()->with($subscriber)->andReturn($silentExceptionHandler);

    $silentExceptionHandler->shouldReceive('handle')->once()->withArgs(
        fn(Throwable $exception) => $exception instanceof $exceptionType 
                                        &&
                                    str_contains(
                                        haystack: $exception->getMessage(), 
                                        needle: $exceptionMessage
                                    )
    );

    $eventHandler->handle($firstArgument, $secondArgument);
    //with this expectation we make sure that execution can still continue
    expect(true)->toBeTrue();
})->with(fn() => a(
    insideExecute: a(
        subscriber: new SubscriberThatThrowsExceptionOnExecuteFake,
        exceptionType: ExceptionFake::class,
        exceptionMessage: 'Opps, runtime exception inside SubscriberThatThrowsExceptionOnExecuteFake::execute'
    ),
    insideCreateEventArguments: a(
        subscriber: new SubscriberThatThrowsExceptionInsideCreateEventArgumentsFake,
        exceptionType: ExceptionFake::class,
        exceptionMessage: 'Opps, an exception was thrown when trying to create the arguments for this event!'
    ),
    whenNotPassingTheCorrectArgumentsToCreateEventArguments: a(
        subscriber: new SubscriberThatThrowsExceptionWhenInvalidArguemntsToCreateEventArgumentsFake,
        exceptionType: ArgumentCountError::class,
        exceptionMessage: ''
    ),
    insideVaidation: a(
        subscriber: new SubscriberThatThrowsExceptionInsideValidation,
        exceptionType: ExceptionFake::class,
        exceptionMessage: 'Opps, an exception was thrown when trying to validate this event!'
    ) 
));

it('will NOT catch ANY exceptions thrown by the ExceptionHandler', function() {
    /** @var \Mockery\MockInterface&ExceptionHandlerFactory */
    (object) $exceptionHandlerFactory = Mockery::mock(ExceptionHandlerFactory::class);
    /** @var \Mockery\MockInterface&Subscriber */
    (object) $subscriber = Mockery::mock(SubscriberThatThrowsExceptionFake::class);
    /** @var \Mockery\MockInterface&ExceptionHandler */
    (object) $exceptionHandler = Mockery::mock(ExceptionHandler::class);
    /** @var \Mockery\MockInterface&Validator */
    (object) $validator = Mockery::mock(Validator::class);
    (object) $exception = new ExceptionFake('thrown exception inside subscrier!');

    (object) $eventHandler = new EventHandler(
        subscriber: $subscriber,
        exceptionHandlerFactory: $exceptionHandlerFactory
    );

    $subscriber->shouldReceive('createEventArguments');
    $subscriber->shouldReceive('validator')->andReturn($validator);
    $subscriber->shouldReceive('execute')->once()->andThrow($exception);

    $validator->shouldReceive('isValid')->andReturn(true);

    $exceptionHandlerFactory->shouldReceive('create')->once()->with($subscriber)->andReturn($exceptionHandler);

    $exceptionHandler->shouldReceive('handle')->once()->with($exception)->andThrow(new ExceptionFake('the event handler threw an exception!'));

    $eventHandler->handle();
})->throws(ExceptionFake::class, exceptionMessage: 'the event handler threw an exception!');

it('will catch and dispatch the exception generated inside the subscriber to the onException method of the Subscriber because it implements ManuallyHandleExceptions, since onException() *chooses* not to throw any further exception, the program is allowed to continue execution.', function() {
    (object) $subscriber = $this->getMockBuilder(SubscriberThatThrowsExceptionOnExecuteAndThatManuallyHandlesTheExceptionFake::class
    )->onlyMethods(['onException'])->getMock();

    /** @var \Mockery\MockInterface&ExceptionHandlerFactory */
    (object) $exceptionHandlerFactory = Mockery::mock(ExceptionHandlerFactory::class);
    /** @var \Mockery\MockInterface&SilentExceptionHandler */
    (object) $manualExceptionHandler = Mockery::mock(SilentExceptionHandler::class);
    (integer) $firstArgument = 10;
    (object) $secondArgument = o(name: 'Rafael');

    (object) $eventHandler = new EventHandler(
        subscriber: $subscriber,
        exceptionHandlerFactory: $exceptionHandlerFactory
    );

    $exceptionHandlerFactory->shouldReceive('create')->once()->with($subscriber)->andReturn(new ManualExceptionHandler($subscriber));


    $subscriber->expects($this->once())->method('onException')->with($this->callback(
        fn($exception) => $exception->getMessage() === 'Opps, runtime exception inside execute(), should be handled manually by this very own subscriber!'
    ));

    $eventHandler->handle($firstArgument, $secondArgument);
    //with this expectation we make sure that execution can still continue
    expect(true)->toBeTrue();
});

it('will catch and dispatch the exception generated inside the subscriber to the onException method of the Subscriber because it implements ManuallyHandleExceptions, since onException() throws yet another exception, the program is halted, this is by design.', function() {
    (object) $subscriber =new SubscriberThatThrowsExceptionOnExecuteAndThatManuallyHandlesTheExceptionAndThrowsAnotherExceptionFake;

    /** @var \Mockery\MockInterface&ExceptionHandlerFactory */
    (object) $exceptionHandlerFactory = Mockery::mock(ExceptionHandlerFactory::class);
    /** @var \Mockery\MockInterface&SilentExceptionHandler */
    (object) $manualExceptionHandler = Mockery::mock(SilentExceptionHandler::class);
    (integer) $firstArgument = 10;
    (object) $secondArgument = o(name: 'Rafael');

    (object) $eventHandler = new EventHandler(
        subscriber: $subscriber,
        exceptionHandlerFactory: $exceptionHandlerFactory
    );

    $exceptionHandlerFactory->shouldReceive('create')->once()->with($subscriber)->andReturn(new ManualExceptionHandler($subscriber));

    $eventHandler->handle($firstArgument, $secondArgument);
})->throws(
    ExceptionFake::class, 
    exceptionMessage: 'thrown again inside onException, should NOT be handled anyomre by the EventHanlder'
);
