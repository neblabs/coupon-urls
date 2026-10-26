<?php

namespace CouponURLs\tests\original\collections;

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Presentation\AttributesManager;
use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\Original\Characters\StringManager;
use function CouponURLs\Original\Utilities\Collection\_a;

Class StringManagerTest extends \WP_UnitTestcase
{
    /**
     * @dataProvider emptyProvider
     */
    public function test_checks_empty($value, $isEmpty)
    {
        (object) $stringManager = new StringManager($value);

        $this->assertSame($isEmpty, $stringManager->isEmpty());
    }

    /**
     * @dataProvider valueCheckProvider
     */
    public function test_checks_equals_value($value, $valueToCheck, $result)
    {
        (object) $stringManager = new StringManager($value);

        $this->assertSame($result, $stringManager->__is($valueToCheck));
    }

    public function test_check_is_value_case_insensitive()
    {
        (object) $name = new StringManager('LeA');

        $this->assertFalse($name->__is('lea'));
        $this->assertTrue($name->__is('lea', $caseInsensitive = true));
    }

    /**
     * @dataProvider valueNOTCheckProvider
     */
    public function test_checks_NOT_equals_value($value, $valueToCheck, $result)
    {
        (object) $stringManager = new StringManager($value);

        $this->assertSame($result, $stringManager->isNot($valueToCheck));
    }

    public function test_gets_string()
    {
        (object) $stringManager = new StringManager('the text');

        $this->assertIsString($stringManager->get());
        $this->assertSame('the text', $stringManager->get());
    }

    /**
     * @dataProvider explodeProvider
     */
    public function test_explodes_string_into_collection($stringValue, $delimiter, Array $result)
    {
        (object) $stringManager = new StringManager($stringValue);

        (object) $pieces = _a($stringManager->explode($delimiter));

        $this->assertInstanceof(Collection::class, $pieces);
        $this->assertEquals($result, $pieces->asArray());
    }


    public function test_matches_a_regular_expression()
    {
        (object) $stringManager = new StringManager('new-path');

        $this->assertTrue($stringManager->matchesRegEx('/([A-Za-z0-9\-]+)/'));
    }

    public function test_gets_all_matches_of_a_rgeular_expression() : void
    {
        (object) $stringManager = new StringManager('=very= awesome =dog=');

        $this->assertSame(
            [
                [
                    '=very=', 
                    '=dog='
                ]
            ], 
            $stringManager->allMatches('/(=\w+=)+/')->asArray()
        );
    }

    /**
    * @dataProvider isEitherProvider
    */
    public function test_string_is_equal_to_either_of_the_given_strings($stringsToCheck, $result)
    {
        (object) $stringManager = new StringManager('keynote');

        return $this->assertSame($result, $stringManager->isEither($stringsToCheck));
    }

    /**
    * @dataProvider isNOTEitherProvider
    */
    public function test_string_is_NOT_equal_to_either_of_the_given_strings($stringsToCheck, $result)
    {
        (object) $stringManager = new StringManager('keynote');

        return $this->assertSame($result, $stringManager->isNotEither($stringsToCheck));
    }

    /**
    * @dataProvider alphnumProvider
    */
    public function test_gets_alphanumeric_chars_only($string, $expectedString)
    {
        (object) $stringManager = new StringManager($string);

        $this->assertInstanceof(StringManager::class, $stringManager->getAlphanumeric());
        $this->assertSame($expectedString, $stringManager->getAlphanumeric()->get());
    }

    public function test_decodes_url()
    {
        (object) $stringManager = new StringManager('users%2Fall');

        $this->assertInstanceof(StringManager::class, $stringManager->decodeUrl());
        $this->assertSame('users/all', $stringManager->decodeUrl()->get());
    }

    /**
    * @dataProvider StringToNativeProvider
    */
    public function test_converts_string_to_native($value, $expectedResult)
    {
        if (StringManager::stringToNative($value) instanceof Collection) {
            $this->assertEquals($expectedResult, StringManager::stringToNative($value));
        } else {
            $this->assertSame($expectedResult, StringManager::stringToNative($value));
        }
    }
    
    public function isNOTEitherProvider()
    {
        return [
            'first occurence --> false' => [
                $stringsToCheck = [
                    'keynote', 'code'
                ],
                $result = false
            ],
            'second occurence --> false' => [
                $stringsToCheck = [
                    'code', 'keynote'
                ],
                $result = false
            ],
            'only occurence --> false' => [
                $stringsToCheck = [
                    'keynote'
                ],
                $result = false
            ],
            'ocurrence different case --> false' => [
                $stringsToCheck = [
                    'kEYNoTE', 'code'
                ],
                $result = false
            ],
            'ArrayRepresentation --> false' => [
                $stringsToCheck = new Collection([
                    'keynote', 'code'
                ]),
                $result = false
            ],
            'empty array --> true' => [
                $stringsToCheck = [
                ],
                $result = true
            ],
            'no ocurrence --> true' => [
                $stringsToCheck = [
                    'toothpaste', 'code'
                ],
                $result = true
            ],
            'ocurrence but with whitespace --> true' => [
                $stringsToCheck = [
                    ' keynote ', 'code'
                ],
                $result = true
            ]
        ];   
    }
    public function isEitherProvider()
    {
        return [
            'first occurence --> true' => [
                $stringsToCheck = [
                    'keynote', 'code'
                ],
                $result = true
            ],
            'second occurence --> true' => [
                $stringsToCheck = [
                    'code', 'keynote'
                ],
                $result = true
            ],
            'only occurence --> true' => [
                $stringsToCheck = [
                    'keynote'
                ],
                $result = true
            ],
            'ocurrence different case --> true' => [
                $stringsToCheck = [
                    'kEYNoTE', 'code'
                ],
                $result = true
            ],
            'string manager --> true' => [
                $stringsToCheck = [
                    new StringManager('keynote'), 'code'
                ],
                $result = true
            ],
            'ArrayRepresentation --> true' => [
                $stringsToCheck = new Collection([
                    'keynote', 'code'
                ]),
                $result = true
            ],
            'empty array --> false' => [
                $stringsToCheck = [
                ],
                $result = false
            ],
            'no ocurrence --> false' => [
                $stringsToCheck = [
                    'toothpaste', 'code'
                ],
                $result = false
            ],
            'ocurrence but with whitespace --> false' => [
                $stringsToCheck = [
                    ' keynote ', 'code'
                ],
                $result = false
            ]
        ];   
    }

    public function explodeProvider()
    {
        return [
            'spaces' => [
                $stringValue = 'the text with internal spaces',
                $delimiter = ' ',
                $result = [
                    new StringManager('the'), 
                    new StringManager('text'), 
                    new StringManager('with'), 
                    new StringManager('internal'), 
                    new StringManager('spaces')
                ]
            ],
            'comas' => [
                $stringValue = 'the,text,with,internal,comas',
                $delimiter = ',',
                $result = [
                    new StringManager('the'), 
                    new StringManager('text'), 
                    new StringManager('with'), 
                    new StringManager('internal'), 
                    new StringManager('comas')
                ]
            ],
            'empty string' => [
                $stringValue = '',
                $delimiter = ',',
                $result = [
                ]
            ]
        ];
    }

    public function emptyProvider()
    {
        return [
            'empty string --> empty' => [
                $value = '',
                $isEmpty = true
            ],
            'empty string spaces --> empty' => [
                $value = '  ',
                $isEmpty = true
            ],
            'empty string new lines --> empty' => [
                $value = "\n",
                $isEmpty = true
            ],
            'empty string tabs --> empty' => [
                $value = "\t",
                $isEmpty = true
            ],
            'null --> empty' => [
                $value = null,
                $isEmpty = true
            ],
            'filled string --> not empty' => [
                $value = 'value',
                $isEmpty = false
            ],
            'filled string integer --> not empty' => [
                $value = "1",
                $isEmpty = false
            ],
            'positive integer --> not empty' => [
                $value = 1,
                $isEmpty = false
            ]
        ];
    }

    public function valueCheckProvider()
    {
        return [
            'same value --> true' => [
                $value = 'stringVal',
                $valueToCheck = 'stringVal',
                $result = true
            ],
            'same text with spaces on original --> false' => [
                $value = ' stringVal',
                $valueToCheck = 'stringVal',
                $result = false
            ],
            'same text, different case --> false' => [
                $value = ' stringVal',
                $valueToCheck = 'STRINGVAL',
                $result = false
            ],
            'different value --> false' => [
                $value = 'stringVal',
                $valueToCheck = 'not a string',
                $result = false
            ],
            '__toString object --> true' => [
                $value = 'stringVal',
                $valueToCheck = new StringManager('stringVal'),
                $result = true
            ],
            'array --> false' => [
                $value = 'stringVal',
                $valueToCheck = ['stringVal'],
                $result = false
            ]   
        ];   
    }

    public function valueNOTCheckProvider()
    {
        return [
            'same value --> false' => [
                $value = 'stringVal',
                $valueToCheck = 'stringVal',
                $result = false
            ],
            'same text with spaces on original --> true' => [
                $value = ' stringVal',
                $valueToCheck = 'stringVal',
                $result = true
            ],
            'same text, different case --> true' => [
                $value = ' stringVal',
                $valueToCheck = 'STRINGVAL',
                $result = true
            ],
            'different value --> true' => [
                $value = 'stringVal',
                $valueToCheck = 'not a string',
                $result = true
            ],
            '__toString object --> false' => [
                $value = 'stringVal',
                $valueToCheck = new StringManager('stringVal'),
                $result = false
            ],
            'array --> true' => [
                $value = 'stringVal',
                $valueToCheck = ['stringVal'],
                $result = true
            ]   
        ];   
    }

    public function alphnumProvider()
    {
        return [
            'all letters' => [
                $string = 'all letters', 
                $expectedString = 'all letters'
            ],
            'all with numbers and other characters' => [
                $string = 'all89/&tg4$_"\'!', 
                $expectedString = 'all89tg4_'
            ]
        ];   
    }

    public function StringToNativeProvider()
        {
            return [
                'string manager to string' => [
                    $value = new StringManager('two'),
                    $expectedResult = 'two'
                ],
                'integer returned as is' => [
                    $value = 1,
                    $expectedResult = 1
                ],
                'null returned as is' => [
                    $value = null,
                    $expectedResult = null
                ],
                'boolean true returned as is' => [
                    $value = true,
                    $expectedResult = true
                ],
                'boolean false returned as is' => [
                    $value = false,
                    $expectedResult = false
                ],
                'empty array returned as is' => [
                    $value = [],
                    $expectedResult = []
                ],
                'object same instance returned' => [
                    $value = (object) [],
                    $expectedResult = $value
                ],
                'array with string manager converted' => [
                    $value = [new StringManager('two')],
                    $expectedResult = ['two']
                ],
                'array with string manager converted recursive' => [
                    $value = [new StringManager('two'), [new StringManager('three')]],
                    $expectedResult = ['two', ['three']]
                ],
                'array with string manager converted -- COLLECTION version' => [
                    $value = new Collection([new StringManager('two')]),
                    $expectedResult = new Collection(['two'])
                ],
                'array with string manager converted recursive -- COLLECTION version' => [
                    $value = new Collection([new StringManager('two'), new Collection([new StringManager('three')])]),
                    $expectedResult = new Collection(['two', new Collection([new StringManager('three')])])
                ],
            ];   
        }
            
}
