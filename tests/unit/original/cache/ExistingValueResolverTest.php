<?php

namespace CouponURLs\tests\original\collections;

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\Original\Cache\ExistingValueResolver;

Class ExistingValueResolverTest extends \WP_UnitTestcase
{
    public function test_call_closure()
    {
        (object) $exisitingValueResolver = new ExistingValueResolver([
            'key' => 'name',
            'data' => new Collection([]),
        ]);

        (object) $closure = function() {
            return 'called';
        };

        $this->assertSame('called', $exisitingValueResolver->call($closure));
    }
    
    public function test_returns_existing_value()    
    {
        (object) $data = new Collection([
            'name' => 'Rafael'
        ]);

        (object) $closure = function() {
            throw new  \Exception("Must not be called");
            ;
        };

        (object) $exisitingValueResolver = $this->getMockBuilder(ExistingValueResolver::class)
                                                ->setConstructorArgs([
                                                    [
                                                        'key' => 'name',
                                                        'data' => $data,
                                                    ]
                                                ])
                                                ->onlyMethods(['call'])
                                                ->getMock();

        $exisitingValueResolver->expects($this->never())
                               ->method('call')
                               ->with($closure)
                               ->will($this->returnCallback($closure));

        $result = $exisitingValueResolver->otherwise($closure);

        $this->assertSame('Rafael', $result);
    }

    public function test_returns_value_from_closure_and_saves_it_for_later()    
    {
        (object) $data = new Collection([
            'name' => 'Rafael'
        ]);
        
        (integer) $count = 0;

        (object) $closure = function() use ($count) {
            $count++;

            return "hello from the closure {$count}";            
        };

        (object) $exisitingValueResolver = $this->getMockBuilder(ExistingValueResolver::class)
                                                ->setConstructorArgs([
                                                    [
                                                        'key' => 'city',
                                                        'data' => $data,
                                                    ]
                                                ])
                                                ->onlyMethods(['call'])
                                                ->getMock();

        $exisitingValueResolver->expects($this->once())
                               ->method('call')
                               ->with($closure)
                               ->will($this->returnCallback($closure));

        $result = $exisitingValueResolver->otherwise($closure);

        $this->assertSame('hello from the closure 1', $result);

        $result2 = $exisitingValueResolver->otherwise($closure);

        $this->assertSame('hello from the closure 1', $result2);

    }

}
