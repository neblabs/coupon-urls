<?php

namespace {
use CouponURLs\Tests\IntegrationTestCase;
use CouponURLs\Tests\UnitTestCase;

require_once 'bootstrap.php';

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

//uses(UnitTestCase::class)->in('unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function dp(...$values) {
    dr(...$values);
    exit;
}

function dr(...$values) : mixed {
    wp_ob_end_flush_all();
    //ob_end_flush();
    //ob_end_clean();
    //ob_implicit_flush(true);

    foreach ($values as $value) {
        var_dump($value);
    }

    return $values[0];
}

function pit(string $title, callable $callable) {
    return it($title, function(...$arguments) use ($callable) {
        buffer();
        $callable(...$arguments);
    });
}

function _it(string $name, callable $test) {
    return it($name, $test)->skip('manually skipped');
}

register_shutdown_function(function() {
    $error = error_get_last();
    if (!$error) {
        return;
    }

    (boolean) $isnotAFatalError = in_array($error['type'], [E_NOTICE, E_WARNING, E_CORE_WARNING, E_COMPILE_WARNING, E_USER_WARNING, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED]);
    
    if ($isnotAFatalError) {
        return;
    }
    ob_end_flush(); ob_end_clean(); ob_implicit_flush(true);

    echo "'[MANUAL ERROR]: {$error['message']}\n\n\n";
    echo "at: {$error['file']}:{$error['line']}";
});

}

namespace CouponURLs\Tests {
    use Mockery\Mock;
    use Mockery\MockInterface;
    use Pest\PendingCalls\TestCall;

    /**
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface
     */
    function mock(string $object, array $constructorArguments = [])
    {
        if ($constructorArguments) {
            (object) $mock = \Mockery::mock($object, $constructorArguments);
        } else {
            (object) $mock = \Mockery::mock($object);
        }

        return $mock->shouldAllowMockingProtectedMethods();
    }
}

namespace {

    use CouponURLs\App\Tests\Abilities\CreatedEntity;
    use CouponURLs\App\Tests\Events\AutomatedEmailEntityCreator;
    use CouponURLs\App\Tests\Events\DataInCreatedEntityCreatorAsserter;
    use CouponURLs\App\Tests\Events\PostEntityCreator;
    use CouponURLs\App\Tests\Events\UserEntityCreator;
    use CouponURLs\App\Tests\Events\WordPressEntitiesCreator;
    use CouponURLs\App\Tests\Events\WordPressMailSentAsserter;
    use CouponURLs\App\Tests\Events\WordPressUpdatePostRunner;
    use CouponURLs\App\Tests\EventTestRunner;
    use CouponURLs\Original\Collections\Collection;
    use Mockery\CompositeExpectation;
    use Mockery\Expectation;
    use Pest\PendingCalls\TestCall;

    use function CouponURLs\Original\Utilities\Collection\_;

    function ordered(Expectation|CompositeExpectation $expectation) : Expectation|CompositeExpectation {
        return $expectation->globally()->ordered();
    }

    function itMaySendEmails(string $description) : TestCall {
        return it($description, function(array $eventRunners, array $data) {
            (object) $eventTestRunner = new EventTestRunner;

            $eventTestRunner->setEntitiesCreator(
                new WordPressEntitiesCreator(
                    entityCreators: _(
                        new DataInCreatedEntityCreatorAsserter(
                            new PostEntityCreator($this::class::factory())
                        ),
                        new UserEntityCreator($this::class::factory()),
                        new AutomatedEmailEntityCreator($this::class::factory())
                    ) 
                )
            );
            //create the automated email
            $eventTestRunner->setAsserter(
                new WordPressMailSentAsserter($this)
            );

            $eventTestRunner->setEventRunners($eventRunners);

            $eventTestRunner->run($data);
        });
    }

    function logUserFromCreatedEntities(string $userEntityTypeid, Collection $createdEntities) : void {
        /** @var CreatedEntity */
        (object) $PostAuthorEntity = $createdEntities->find(
            fn(CreatedEntity $createdEntity) => $createdEntity->entityTypeId()->is($userEntityTypeid)
        );

        wp_set_current_user($PostAuthorEntity->createdEntityObject()->ID);
        wp_set_auth_cookie($PostAuthorEntity->createdEntityObject()->ID);
    }
}