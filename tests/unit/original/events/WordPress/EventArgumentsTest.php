<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Events\WordPress;

use CouponURLs\Original\Events\Wordpress\EventArguments;
use stdClass;
use WP_Post;
use WP_UnitTestCase;
use WP_User;

Class EventArgumentsTest extends WP_UnitTestCase
{
    public function set_up()
    {
        parent::set_up();
        
        //...
    }
    
    public function test_multidemisonal_arrays_get_convetred_to_stdclasses()
    {
        (object) $user = $this->createMock(WP_User::class);
        (object) $arguemnts = [
            'one' => [
                'two' => [
                    'three' => [
                        'deppestUser' => $user
                    ]
                ],
                'anotherUser' => $user
            ],
            'numbers' => [1, 2, 3],
            // make sure types are not converted to stdClass
            'user' => $user
        ];
        (object) $eventArguments = new EventArguments($arguemnts);

        $this->assertInstanceOf(expected: stdClass::class, actual: $eventArguments->get('one'));
        $this->assertInstanceOf(expected: stdClass::class, actual: $eventArguments->get('one')->two);
        $this->assertInstanceOf(expected: stdClass::class, actual: $eventArguments->get('one')->two->three);


        //make sure indexed arrays dont get converted to objects
        $this->assertIsArray($eventArguments->get('numbers'));
        $this->assertSame([1, 2, 3], $eventArguments->get('numbers'));
        //types should be preserved
        $this->assertInstanceOf(expected: WP_User::class, actual: $eventArguments->get('user'));
        $this->assertInstanceOf(expected: WP_User::class, actual: $eventArguments->get('one')->anotherUser);
        $this->assertInstanceOf(expected: WP_User::class, actual: $eventArguments->get('one')->two->three->deppestUser);
    }


}