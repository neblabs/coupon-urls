<?php

namespace CouponURLs\tests\original\collections;

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Data\Drivers\WordPressDatabaseDriver;
use CouponURLs\Original\Data\Schema\DatabaseCredentials;
use CouponURLs\Original\Characters\StringManager;

Class WordPressDatabaseDriverTest extends \WP_UnitTestCase
{
    public function test_throws_exception_if_like_has_not_been_prepared()
    {
        $this->expectException(\Exception::class);

        (object) $wordPressDatabaseDriver = new WordPressDatabaseDriver();

        $wordPressDatabaseDriver->escapeLike('post');
    }

    public function test_throws_exception_if_like_has_not_been_prepared_different_value()
    {
        $this->expectException(\Exception::class);

        (object) $wordPressDatabaseDriver = new WordPressDatabaseDriver();

        $wordPressDatabaseDriver->getLIKEPlaceHolder('page');
        $wordPressDatabaseDriver->escapeLike('post');
    }

    public function test_throws_NO_exception_after_like_has_been_prepared()
    {
        (object) $wordPressDatabaseDriver = new WordPressDatabaseDriver();

        $wordPressDatabaseDriver->getLIKEPlaceHolder('post');
        $wordPressDatabaseDriver->escapeLike('post');
    }

    public function test_calls_wpdb_esc_like()
    {
        (object) $wordPressDatabaseDriver = $this->getMockBuilder(WordPressDatabaseDriver::class)
                                                 ->setMethodsExcept([
                                                    'getLIKEPlaceHolder',
                                                    'escapeLike'
                                                ])
                                                 ->getmock();

        (string) $valueToBeEscaped = 'post';

        (object) $wpdbMock = $this->createMock(\wpdb::class);

        $wpdbMock->expects($this->once())
                 ->method('esc_like')
                 ->with($valueToBeEscaped);

        $wordPressDatabaseDriver->wpdb = $wpdbMock;
        
        $wordPressDatabaseDriver->getLIKEPlaceHolder($valueToBeEscaped);
        $wordPressDatabaseDriver->escapeLike($valueToBeEscaped);
    }

    /**
     * @dataProvider likePlaceholderProvider
     */
    public function test_returns_right_like_placeholder($likeValue, $expectedLikePlaceholder)
    {
        (object) $wordPressDatabaseDriver = new WordPressDatabaseDriver();

        $this->assertSame(
            $expectedLikePlaceholder,
            $wordPressDatabaseDriver->getLIKEPlaceHolder($likeValue)
        );
    }

    /**
    * @dataProvider ErrorsProvider
    */
    public function test_errors(Callable $beforeCheck, $hasErrors, Array $expectedErrors)
    {
        $beforeCheck();

        (object) $errors = WordPressDatabaseDriver::errors();

        $this->assertInstanceOf(Collection::class, $errors); 
        $this->assertSame($hasErrors, $errors->haveAny()); 
        $this->assertEquals($expectedErrors, $errors->asArray()); 

        $GLOBALS['EZSQL_ERROR'] = [];
    }
    
    public function ErrorsProvider()
    {
        return [
            'global errors unset' => [
                $beforeCheck = function(){
                    unset($GLOBALS['EZSQL_ERROR']);
                },
                $hasErrors = false,
                $expectedErrors = [
                ]
            ],
            'global errors set but empty' => [
                $beforeCheck = function(){
                    $GLOBALS['EZSQL_ERROR'] = [];
                },
                $hasErrors = false,
                $expectedErrors = [
                ]
            ],
            'global errors set with an error' => [
                $beforeCheck = function(){
                    $GLOBALS['EZSQL_ERROR'] = [
                        [
                            'query' => "UPDATE couponsplus_url_types SET  WHERE id = 4 LIMIT 1",
                            'error_str' => "You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'WHERE id = 4 LIMIT 1' at line 1"
                        ]
                    ];
                },
                $hasErrors = true,
                $expectedErrors = [
                    new Collection ([
                        'query' => new StringManager("UPDATE couponsplus_url_types SET  WHERE id = 4 LIMIT 1"),
                            'error_str' => new StringManager("You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'WHERE id = 4 LIMIT 1' at line 1")
                    ])
                ]
            ]
        ];   
    }
    
    public function likePlaceholderProvider()
    {
        return [
            'null value ---> cancel query' => [
                $likeValue = null, 
                $expectedLikePlaceholder = '= ? AND 1=0'
            ],
            'empty string value ---> cancel query' => [
                $likeValue = '', 
                $expectedLikePlaceholder = '= ? AND 1=0'
            ],
            'filled string value ---> proceed with query' => [
                $likeValue = 'value', 
                $expectedLikePlaceholder = 'LIKE ?'
            ],
            'zero integer value ---> proceed with query' => [
                $likeValue = 0, 
                $expectedLikePlaceholder = 'LIKE ?'
            ],
            'negative integer value ---> proceed with query' => [
                $likeValue = -1, 
                $expectedLikePlaceholder = 'LIKE ?'
            ],
            'positive integer value ---> proceed with query' => [
                $likeValue = 1, 
                $expectedLikePlaceholder = 'LIKE ?'
            ]
        ];     
    }
}
