<?php

namespace CouponURLs\tests\original\collections;

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\Original\Characters\StringManager;
use CouponURLs\Original\Collections\Mapper\Types;
use CouponURLs\Original\Collections\Mapper\Types\StringType;

Class TypesTest extends \WP_UnitTestcase
{
    public function test_returns_string()
    {
        (object) $type = Types::STRING();

        $this->assertInstanceOf(Types::class, $type);
        $this->assertEquals(Types::STRING, $type->getType());
        $this->assertTrue($type->is(Types::STRING));
    }

    public function test_returns_integer()
    {
        (object) $type = Types::INTEGER();

        $this->assertInstanceOf(Types::class, $type);
        $this->assertEquals(Types::INTEGER, $type->getType());
        $this->assertTrue($type->is(Types::INTEGER));
    }

    public function test_returns_boolean()
    {
        (object) $type = Types::BOOLEAN();

        $this->assertInstanceOf(Types::class, $type);
        $this->assertEquals(Types::BOOLEAN, $type->getType());
        $this->assertTrue($type->is(Types::BOOLEAN));
    }

    /**
    * @dataProvider BooleanTestCorrectTypeProvider
    */
    public function test_boolean_is_correct_type($value, $isCorrectType)
    {
        (object) $type = Types::BOOLEAN();

        $this->assertSame($isCorrectType, $type->isCorrectType($value));
    }

    /**
    * @dataProvider CollectionTestCorrectTypeProvider
    */
    public function test_collection_is_correct_type($value, $isCorrectType)
    {
        (object) $type = Types::COLLECTION();

        $this->assertSame($isCorrectType, $type->isCorrectType($value));
    }

    /**
    * @dataProvider IntegerTestCorrectTypeProvider
    */
    public function test_integer_is_correct_type($value, $isCorrectType)
    {
        (object) $type = Types::INTEGER();

        $this->assertSame($isCorrectType, $type->isCorrectType($value));
    }

    /**
    * @dataProvider StringTestCorrectTypeProvider
    */
    public function test_string_is_correct_type($value, $isCorrectType)
    {
        (object) $type = Types::STRING();

        $this->assertSame($isCorrectType, $type->isCorrectType($value));
    }

    /**
    * @dataProvider stringDefaultValueProvider
    */
    public function test_STRING_has_default_value($defaultValue)
    {
        (object) $type = Types::STRING()->withDefault($defaultValue);

        $this->assertInstanceOf(Types::class, $type);
        $this->assertEquals(Types::STRING, $type->getType());
        $this->assertTrue($type->hasDefaultvalue());
    }

    /**
    * @dataProvider stringInvalidDefaultValueProvider
    */
    public function test_STRING_has_no_default_value($defaultValue)
    {
        (object) $type = Types::STRING()->withDefault($defaultValue);

        $this->assertInstanceOf(Types::class, $type);
        $this->assertEquals(Types::STRING, $type->getType());
        $this->assertFalse($type->hasDefaultvalue());
    }

    /**
    * @dataProvider integerDefaultValueProvider
    */
    public function test_INTEGER_has_default_value($defaultValue)
    {
        (object) $type = Types::INTEGER()->withDefault($defaultValue);

        $this->assertInstanceOf(Types::class, $type);
        $this->assertEquals(Types::INTEGER, $type->getType());
        $this->assertTrue($type->hasDefaultvalue());
    }

    /**
    * @dataProvider integerInvalidDefaultValueProvider
    */
    public function test_INTEGER_has_no_default_value($defaultValue)
    {
        (object) $type = Types::INTEGER()->withDefault($defaultValue);

        $this->assertInstanceOf(Types::class, $type);
        $this->assertEquals(Types::INTEGER, $type->getType());
        $this->assertFalse($type->hasDefaultvalue());
    }

    /**
    * @dataProvider booleanDefaultValueProvider
    */
    public function test_BOOLEAN_has_default_value($defaultValue)
    {
        (object) $type = Types::BOOLEAN()->withDefault($defaultValue);

        $this->assertInstanceOf(Types::class, $type);
        $this->assertEquals(Types::BOOLEAN, $type->getType());
        $this->assertTrue($type->hasDefaultvalue());
    }

    /**
    * @dataProvider booleanInvalidDefaultValueProvider
    public function test_BOOLEAN_has_no_default_value($defaultValue)
    {
        (object) $type = Types::BOOLEAN()->withDefault($defaultValue);

        $this->assertInstanceOf(Types::class, $type);
        $this->assertEquals(Types::BOOLEAN, $type->getType());
        $this->assertFalse($type->hasDefaultValue());
    }
    */

    /**
    * @dataProvider stringDefaultValueProvider
    */
    public function test_STRING_get_default_value($defaultValue)
    {
        (object) $type = Types::STRING()->withDefault($defaultValue);

        $this->assertInstanceOf(Types::class, $type);
        $this->assertEquals(Types::STRING, $type->getType());
        $this->assertEquals($defaultValue, $type->getDefaultValue());
    }

    /**
    * @dataProvider integerDefaultValueProvider
    */
    public function test_INTEGER_get_default_value($defaultValue)
    {
        (object) $type = Types::INTEGER()->withDefault($defaultValue);

        $this->assertInstanceOf(Types::class, $type);
        $this->assertEquals(Types::INTEGER, $type->getType());
        $this->assertEquals($defaultValue, $type->getDefaultValue());
    }

    /**
    * @dataProvider booleanDefaultValueProvider
    */
    public function test_BOOLEAN_get_default_value($defaultValue)
    {
        (object) $type = Types::BOOLEAN()->withDefault($defaultValue);

        $this->assertInstanceOf(Types::class, $type);
        $this->assertEquals(Types::BOOLEAN, $type->getType());
        $this->assertEquals($defaultValue, $type->getDefaultValue());
    }

    /**
    * @dataProvider defaultValuesTakePrecedenceProvider
    */
    public function test_chooses_DEFAULT_value($defaultValue, $newValue)
    {
        (object) $type = Types::STRING()->withDefault($defaultValue);

        $this->assertEquals($defaultValue, $type->pickValue($newValue));
    }

    /**
    * @dataProvider newValuesTakePrecedenceProvider
    */
    public function test_chooses_NEW_value($defaultValue, $newValue)
    {
        (object) $type = Types::STRING()->withDefault($defaultValue);

        $this->assertEquals($newValue, $type->pickValue($newValue));
    }

    /**
    * @dataProvider INTEGERdefaultValuesTakePrecedenceProvider
    */
    public function test_chooses_DEFAULT_value_INTEGER($defaultValue, $newValue)
    {
        (object) $type = Types::INTEGER()->withDefault($defaultValue);

        $this->assertEquals($defaultValue, $type->pickValue($newValue));
    }

    /**
    * @dataProvider INTEGERnewValuesTakePrecedenceProvider
    */
    public function test_chooses_NEW_value_INTEGER($defaultValue, $newValue)
    {
        (object) $type = Types::INTEGER()->withDefault($defaultValue);

        $this->assertEquals($newValue, $type->pickValue($newValue));
    }

    /**
    * @dataProvider BOOLEANdefaultValuesTakePrecedenceProvider
    */
    public function test_chooses_DEFAULT_value_BOOLEAN($defaultValue, $newValue, $expected = null) 
    {
        (object) $type = Types::BOOLEAN()->withDefault($defaultValue);

        $expectedValue = ($expected !== null)? $expected : $defaultValue;

        $this->assertEquals($expectedValue, $type->pickValue($newValue));
    }

    /**
    * @dataProvider BOOLEANnewValuesTakePrecedenceProvider
    */
    public function test_chooses_NEW_value_BOOLEAN($defaultValue, $newValue, $expected = null)
    {
        (object) $type = Types::BOOLEAN()->withDefault($defaultValue);

        $expectedValue = ($expected !== null)? $expected : $defaultValue;

        $this->assertEquals($newValue, $type->pickValue($newValue));
    }

    /**
    * @dataProvider COLLECTIONdefaultValuesTakePrecedenceProvider
    */
    public function test_chooses_DEFAULT_value_COLLECTION($defaultValue, $newValue)
    {
        (object) $type = Types::COLLECTION()->withDefault($defaultValue);

        $this->assertEquals($defaultValue, $type->pickValue($newValue));
    }

    /**
    * @dataProvider isStringProvider
    */
    public function test_is_string($value, $result)
    {
        (object) $isString = StringType::stringsOnly();

        $this->assertSame($result, $isString($value));
    }
    
    public function test_all_allowed_values_if_such_values_have_not_been_specified()
    {
        (object) $type = Types::STRING();

        $this->assertInstanceOf(Types::class, $type);
        $this->assertTrue($type->anyValueIsAllowed());
        $this->assertInstanceOf(Collection::class, $type->getAllowedValues());
        $this->assertSame([], $type->getAllowedValues()->asArray());
    }

    public function test_sets_allowed_values()
    {
        (object) $type = Types::STRING();
        (object) $result = $type->allowed(['name']);

        $this->assertInstanceOf(Types::class, $result);
        $this->assertInstanceOf(Types::class, $type);
        $this->assertSame($type, $result);
        $this->assertFalse($type->anyValueIsAllowed());
        $this->assertInstanceOf(Collection::class, $type->getAllowedValues());
        $this->assertSame(['name'], $type->getAllowedValues()->asArray());
    }

    public function test_sets_allowed_values_as_collection()
    {
        (object) $type = Types::STRING();
        (object) $result = $type->allowed(new Collection(['name']));

        $this->assertInstanceOf(Types::class, $result);
        $this->assertInstanceOf(Types::class, $type);
        $this->assertSame($type, $result);
        $this->assertFalse($type->anyValueIsAllowed());
        $this->assertInstanceOf(Collection::class, $type->getAllowedValues());
        $this->assertSame(['name'], $type->getAllowedValues()->asArray());
    }

    /**
    * @dataProvider colectionFromStringProvider
    */
    public function test_gets_collection_from_string($string, $separator, $expectedArray)
    {
        (object) $type = Types::Collection()->separatedBy($separator);

        $this->assertSame($expectedArray, $type->concretePickValue($string));
    }

    public function test_gets_fallback_value()
    {
        (object) $type = Types::STRING()->withDefault('the default string')->allowed(['one', 'two']);

        $this->assertSame('the default string', (string) $type->getFallbackAllowedValue());
    }

    public function test_gets_fallback_value_when_no_default_has_been_specified()
    {
        (object) $type = Types::STRING()->allowed(['one', 'two']);

        $this->assertSame('one', (string) $type->getFallbackAllowedValue());
    }
    

    public function stringDefaultValueProvider()
    {
        return [
            'a filled string' => ['the default value'],
        ];   
    }

    public function stringInvalidDefaultValueProvider()
    {
        return [
            'an empty string' => [''],
            'a true boolean' => [true],
            'a false boolean' => [false],
            'a compound type | array' => [[]],
            'a compound type with values | array' => [[1, 2, 3, 4]],
            'a compound type | object' => [(object) []],
            'a compound type | object with values' => [(object) [1, 2, 3, 4]],
        ];   
    }

    public function integerDefaultValueProvider()
    {
        return [
            'a zero integer' => [0],
            'a negative integer' => [-1],
            'a positive integer' => [1],
        ];   
    }

    public function integerInvalidDefaultValueProvider()
    {
        return [
            'an empty string' => [''],
            'a filled string' => ['stringy'],
            'a false value' => [false],
            'a true value' => [true],
            'a compound type | array' => [[]],
            'a compound type with values | array' => [[1, 2, 3, 4]],
            'a compound type | object' => [(object) []],
            'a compound type | object with values' => [(object) [1, 2, 3, 4]],
        ];
    }

    public function booleanDefaultValueProvider()
    {
        return [
            'boolean true' => [true],
            'boolean false' => [false],
        ];   
    }

    public function booleanInvalidDefaultValueProvider()
    {
        return [
            'an empty string' => [''],
            'a filled string' => ['stringy'],
            'a zero integer' => [0],
            'a negative integer' => [-1],
            'a positive integer' => [1],
            'a compound type | array' => [[]],
            'a compound type with values | array' => [[1, 2, 3, 4]],
            'a compound type | object' => [(object) []],
            'a compound type | object with values' => [(object) [1, 2, 3, 4]],
        ];   
    }

    public function defaultValuesTakePrecedenceProvider()
    {
        return [
            [
                $defaultValue = 'the default value', 
                $newValue = ''
            ],
            [
                $defaultValue = 'the default value', 
                $newValue = 0
            ],
            [
                $defaultValue = 'the default value', 
                $newValue = 1
            ],
            [
                $defaultValue = 'the default value', 
                $newValue = true
            ],
            [
                $defaultValue = 'the default value', 
                $newValue = false
            ],
            [
                $defaultValue = 'the default value', 
                $newValue = null
            ],
            [
                $defaultValue = 'the default value', 
                $newValue = []
            ],
            [
                $defaultValue = 'the default value', 
                $newValue = [1, 2]
            ],
            [
                $defaultValue = 'the default value', 
                $newValue = (object) []
            ],
            [
                $defaultValue = 'the default value', 
                $newValue = (object) [1, 2]
            ],
        ];   
    }

    public function newValuesTakePrecedenceProvider()
    {
        return [
            [
                $defaultValue = 'the default value', 
                $newValue = 'the new value'
            ],
            [
                $defaultValue = 'the default value', 
                $newValue = 'nv'
            ],
            [
                $defaultValue = 'a', 
                $newValue = 'b'
            ],
            [
                $defaultValue = 'b', 
                $newValue = 'a'
            ],
            [
                $defaultValue = '1', 
                $newValue = '0'
            ],
            [
                $defaultValue = '0', 
                $newValue = '1'
            ],
            [
                $defaultValue = 0, 
                $newValue = 'the new value'
            ],
            [
                $defaultValue = '', 
                $newValue = 'the new value'
            ],
            [
                $defaultValue = true, 
                $newValue = 'the new value'
            ],
            [
                $defaultValue = false,
                $newValue = 'the new value'
            ],
            [
                $defaultValue = null, 
                $newValue = 'the new value'
            ],
            [
                $defaultValue = [], 
                $newValue = 'the new value'
            ],
            [
                $defaultValue = [1, 2],
                $newValue = 'the new value'
            ],
            [
                $defaultValue = (object) [],
                $newValue = 'the new value'
            ],
            [
                $defaultValue = (object) [1, 2],
                $newValue = 'the new value'
            ],
        ];   
    }

    public function INTEGERdefaultValuesTakePrecedenceProvider()
    {
        return [
            [
                $defaultValue = 1, 
                $newValue = ''
            ],
            [
                $defaultValue = 0, 
                $newValue = ''
            ],
            [
                $defaultValue = -1, 
                $newValue = ''
            ],
            [
                $defaultValue = 1, 
                $newValue = 'value'
            ],
            [
                $defaultValue = 0, 
                $newValue = 'value'
            ],
            [
                $defaultValue = -1, 
                $newValue = 'value'
            ],
            [
                $defaultValue = 0, 
                $newValue = true
            ],
            [
                $defaultValue = 0, 
                $newValue = false
            ],
            [
                $defaultValue = 0, 
                $newValue = null
            ],
            [
                $defaultValue = 0, 
                $newValue = []
            ],
            [
                $defaultValue = 0, 
                $newValue = [1, 2]
            ],
            [
                $defaultValue = 0, 
                $newValue = (object) []
            ],
            [
                $defaultValue = 0, 
                $newValue = (object) [1, 2]
            ],
        ];   
    }

    public function INTEGERnewValuesTakePrecedenceProvider()
    {
        return [
            [
                $defaultValue = '', 
                $newValue = 1
            ],
            [
                $defaultValue = '', 
                $newValue = 0
            ],
            [
                $defaultValue = '', 
                $newValue = -1
            ],
            [
                $defaultValue = 1,
                $newValue = '2'
            ],
            [
                $defaultValue = 1,
                $newValue = '0'
            ],
            [
                $defaultValue = 'value', 
                $newValue = 1
            ],
            [
                $defaultValue = 'value', 
                $newValue = 0
            ],
            [
                $defaultValue = 'value', 
                $newValue = -1
            ],
            [
                $defaultValue = true, 
                $newValue = 0
            ],
            [
                $defaultValue = false, 
                $newValue = 0
            ],
            [
                $defaultValue = null, 
                $newValue = 0
            ],
            [
                $defaultValue = [], 
                $newValue = 0
            ],
            [
                $defaultValue = [1, 2], 
                $newValue = 0
            ],
            [
                $defaultValue = (object) [], 
                $newValue = 0
            ],
            [
                $defaultValue = (object) [1, 2], 
                $newValue = 0
            ],
        ];   
    }

    public function BOOLEANdefaultValuesTakePrecedenceProvider()
    {
        return [
            [
                $defaultValue = true, 
                $newValue = ''
            ],
            [
                $defaultValue = true, 
                $newValue = 'value'
            ],
            [
                $defaultValue = false, 
                $newValue = 'value'
            ],
            [
                $defaultValue = "false", 
                $newValue = '',
                $expected = false
            ],
            [
                $defaultValue = "true", 
                $newValue = '',
                $expected = true
            ],
            [
                $defaultValue = "false", 
                $newValue = 'value',
                $expected = false
            ],
            [
                $defaultValue = "true", 
                $newValue = 'value',
                $expected = true
            ],
            [
                $defaultValue = "false", 
                $newValue = 1,
                $expected = false
            ],
            [
                $defaultValue = "true", 
                $newValue = 1,
                $expected = true
            ],
            [
                $defaultValue = "false", 
                $newValue = 0,
                $expected = false
            ],
            [
                $defaultValue = "true", 
                $newValue = 0,
                $expected = true
            ],
            [
                $defaultValue = false, 
                $newValue = ''
            ],
            [
                $defaultValue = false, 
                $newValue = 1
            ],
            [
                $defaultValue = true, 
                $newValue = 1
            ],
            [
                $defaultValue = false, 
                $newValue = 0
            ],
            [
                $defaultValue = true, 
                $newValue = 0
            ],
            [
                $defaultValue = false, 
                $newValue = -1
            ],
            [
                $defaultValue = true, 
                $newValue = -1
            ],
            [
                $defaultValue = false, 
                $newValue = null
            ],
            [
                $defaultValue = true, 
                $newValue = null
            ],
            [
                $defaultValue = false, 
                $newValue = []
            ],
            [
                $defaultValue = true, 
                $newValue = []
            ],
            [
                $defaultValue = false, 
                $newValue = [1, 2]
            ],
            [
                $defaultValue = true, 
                $newValue = [1, 2]
            ],
            [
                $defaultValue = false, 
                $newValue = (object) []
            ],
            [
                $defaultValue = true, 
                $newValue = (object) []
            ],
            [
                $defaultValue = false, 
                $newValue = (object) [1, 2]
            ],
            [
                $defaultValue = true, 
                $newValue = (object) [1, 2]
            ],
        ];   
    }

    public function BOOLEANnewValuesTakePrecedenceProvider()
    {
        return [
            [
                $defaultValue = '', 
                $newValue = true
            ],
            [
                $defaultValue = 'value', 
                $newValue = true
            ],
            [
                $defaultValue = 'value', 
                $newValue = false
            ],
            [
                $defaultValue = false, 
                $newValue = ''
            ],
            [
                $defaultValue = "", 
                $newValue = false,
                $expected = false
            ],
            [
                $defaultValue = '', 
                $newValue = true,
                $expected = true
            ],
            [
                $defaultValue = "value", 
                $newValue = 'false',
                $expected = false
            ],
            [
                $defaultValue = "value", 
                $newValue = 'true',
                $expected = true
            ],
            [
                $defaultValue = 1, 
                $newValue = false,
                $expected = false
            ],
            [
                $defaultValue = 1, 
                $newValue = true,
                $expected = true
            ],
            [
                $defaultValue = 0, 
                $newValue = false,
                $expected = false
            ],
            [
                $defaultValue = 0, 
                $newValue = true,
                $expected = true
            ],
            [
                $defaultValue = "1", 
                $newValue = false,
                $expected = false
            ],
            [
                $defaultValue = "1", 
                $newValue = true,
                $expected = true
            ],
            [
                $defaultValue = "0", 
                $newValue = false,
                $expected = false
            ],
            [
                $defaultValue = "0", 
                $newValue = true,
                $expected = true
            ],

            [
                $defaultValue = 1, 
                $newValue = false
            ],
            [
                $defaultValue = 1, 
                $newValue = true
            ],
            [
                $defaultValue = 0, 
                $newValue = false
            ],
            [
                $defaultValue = 0, 
                $newValue = true
            ],
            [
                $defaultValue = -1, 
                $newValue = false
            ],
            [
                $defaultValue = -1, 
                $newValue = true
            ],
            [
                $defaultValue = false, 
                $newValue = true
            ],
            [
                $defaultValue = true, 
                $newValue = false
            ],
            [
                $defaultValue = null, 
                $newValue = false
            ],
            [
                $defaultValue = null, 
                $newValue = true
            ],
            [
                $defaultValue = [], 
                $newValue = false
            ],
            [
                $defaultValue = [], 
                $newValue = true
            ],
            [
                $defaultValue = [1, 2], 
                $newValue = false
            ],
            [
                $defaultValue = [1, 2], 
                $newValue = true
            ],
            [
                $defaultValue = (object) [], 
                $newValue = false
            ],
            [
                $defaultValue = (object) [], 
                $newValue = true
            ],
            [
                $defaultValue = (object) [1, 2], 
                $newValue = false
            ],
            [
                $defaultValue = (object) [1, 2], 
                $newValue = true
            ],
        ];   
    }

    public function COLLECTIONdefaultValuesTakePrecedenceProvider()
    {
        return [
            [
                $defaultValue = [0, 1], 
                $newValue = ''
            ],
            /*
                since we added support for string representation of arrays/collections
                this test no longer seems valid
             [
                $defaultValue = [0], 
                $newValue = 'value'
            ],
            [
                $defaultValue = [1], 
                $newValue = 'value'
            ],*/
            [
                $defaultValue = [0], 
                $newValue = ''
            ],
            [
                $defaultValue = [1], 
                $newValue = 1
            ],
            [
                $defaultValue = [0], 
                $newValue = 1
            ],
            [
                $defaultValue = [0], 
                $newValue = 0
            ],
            [
                $defaultValue = [1], 
                $newValue = 0
            ],
            [
                $defaultValue = [0], 
                $newValue = -1
            ],
            [
                $defaultValue = [-2], 
                $newValue = -1
            ],
            [
                $defaultValue = [0], 
                $newValue = null
            ],
            [
                $defaultValue = [1], 
                $newValue = null
            ],

            [
                $defaultValue = [], 
                $newValue = (object) []
            ],
            [
                $defaultValue = [1], 
                $newValue = (object) []
            ],
            [
                $defaultValue = [0], 
                $newValue = (object) [1, 2]
            ],
            [
                $defaultValue = [], 
                $newValue = (object) [1, 2]
            ],
        ];           
    }

    public function isStringProvider()
    {
        return [
            'native string ---> true' => [
                $value = 'a native string',
                $isString = true
            ],
            'string manager ---> true' => [
                $value = new StringManager('a native string'),
                $isString = true
            ],
            'integer ---> false' => [
                $value = 11,
                $isString = false
            ],
            'array ---> false' => [
                $value = [],
                $isString = false
            ],
            'object ---> false' => [
                $value = new \StdClass,
                $isString = false
            ]
        ];   
    }
    
    public function BooleanTestCorrectTypeProvider()
    {
        return [
            'null ---> false' => [
                $value = null,
                $isCorrectType = false
            ],
            'empty string ---> false' => [
                $value = '',
                $isCorrectType = false
            ],
            'empty integer ---> false' => [
                $value = 0,
                $isCorrectType = false
            ],
            'empty array ---> false' => [
                $value = [],
                $isCorrectType = false
            ],
            'empty object ---> false' => [
                $value = (object) [],
                $isCorrectType = false
            ],
            'non empty string ---> false' => [
                $value = 'true',
                $isCorrectType = false
            ],
            'non empty string ---> false' => [
                $value = '0',
                $isCorrectType = false
            ],
            'non empty boolean string ---> false' => [
                $value = 'false',
                $isCorrectType = false
            ],
            'non empty integer ---> false' => [
                $value = 1,
                $isCorrectType = false
            ],
            'non empty array ---> false' => [
                $value = [true],
                $isCorrectType = false
            ],
            'boolean true ---> true' => [
                $value = true,
                $isCorrectType = true
            ],
            'boolean false ---> true' => [
                $value = false,
                $isCorrectType = true
            ],
       ];   
    }

    public function CollectionTestCorrectTypeProvider()
    {
        return [
            'null ---> false' => [
                $value = null,
                $isCorrectType = false
            ],
            'empty string ---> false' => [
                $value = '',
                $isCorrectType = false
            ],
            'empty integer ---> false' => [
                $value = 0,
                $isCorrectType = false
            ],
            'empty object ---> false' => [
                $value = (object) [],
                $isCorrectType = false
            ],
            'non empty string ---> false' => [
                $value = 'true',
                $isCorrectType = false
            ],
            'non empty string ---> false' => [
                $value = '0',
                $isCorrectType = false
            ],
            'non empty boolean string ---> false' => [
                $value = 'false',
                $isCorrectType = false
            ],
            'non empty integer ---> false' => [
                $value = 1,
                $isCorrectType = false
            ],
            'boolean true ---> false' => [
                $value = true,
                $isCorrectType = false
            ],
            'boolean false ---> false' => [
                $value = false,
                $isCorrectType = false
            ],
            'empty array ---> true' => [
                $value = [],
                $isCorrectType = true
            ],
            'non empty array ---> true' => [
                $value = [1, 2, 3],
                $isCorrectType = true
            ],
       ];   
    }

    public function IntegerTestCorrectTypeProvider()
    {
        return [
            'null ---> false' => [
                $value = null,
                $isCorrectType = false
            ],
            'empty string ---> false' => [
                $value = '',
                $isCorrectType = false
            ],
            'empty object ---> false' => [
                $value = (object) [],
                $isCorrectType = false
            ],
            'non empty string ---> false' => [
                $value = 'true',
                $isCorrectType = false
            ],
            'non empty boolean string ---> false' => [
                $value = 'false',
                $isCorrectType = false
            ],
            'boolean true ---> false' => [
                $value = true,
                $isCorrectType = false
            ],
            'boolean false ---> false' => [
                $value = false,
                $isCorrectType = false
            ],
            'empty array ---> false' => [
                $value = [],
                $isCorrectType = false
            ],
            'non empty array ---> false' => [
                $value = [1, 2, 3],
                $isCorrectType = false
            ],
            'empty integer ---> true' => [
                $value = 0,
                $isCorrectType = true
            ],
            'negative integer ---> true' => [
                $value = -1,
                $isCorrectType = true
            ],
            'positive integer ---> true' => [
                $value = 1,
                $isCorrectType = true
            ],
            'string integer ---> true' => [
                $value = '1',
                $isCorrectType = true
            ],
            'string empty integer ---> true' => [
                $value = '0',
                $isCorrectType = true
            ],
       ];   
    }
    
    public function StringTestCorrectTypeProvider()
    {
        return [
            'null ---> false' => [
                $value = null,
                $isCorrectType = false
            ],
            'empty integer ---> false' => [
                $value = 0,
                $isCorrectType = false
            ],
            'empty object ---> false' => [
                $value = (object) [],
                $isCorrectType = false
            ],
            'non empty integer ---> false' => [
                $value = 1,
                $isCorrectType = false
            ],
            'boolean true ---> false' => [
                $value = true,
                $isCorrectType = false
            ],
            'boolean false ---> false' => [
                $value = false,
                $isCorrectType = false
            ],
            'empty array ---> false' => [
                $value = [],
                $isCorrectType = false
            ],
            'non empty array ---> false' => [
                $value = [1, 2, 3],
                $isCorrectType = false
            ],
            'empty string ---> true' => [
                $value = '',
                $isCorrectType = true
            ],
            'non empty string ---> true' => [
                $value = 'false',
                $isCorrectType = true
            ],
            'string manager instance ---> true' => [
                $value = new StringManager('false'),
                $isCorrectType = true
            ],
            'string manager instance empty ---> true' => [
                $value = new StringManager(''),
                $isCorrectType = true
            ],
       ];   
    }         

    public function colectionFromStringProvider()
    {
        return [
            'regular string default separator' => [
                $string = 'a string', 
                $separator = null, 
                $expectedArray = ['a string']
            ],
            'string separated by comma, default separator' => [
                $string = 'one, two', 
                $separator = null, 
                $expectedArray = ['one', 'two']
            ],
            'string separated by comma, explicit comma separator' => [
                $string = 'one, two', 
                $separator = ',', 
                $expectedArray = ['one', 'two']
            ],
            'string separated by new line, explicit comma separator' => [
                $string = 'one, two', 
                $separator = "\n", 
                $expectedArray = ['one, two']
            ],
            'string separated by new line, explicit new line separator' => [
                $string = "one\ntwo", 
                $separator = "\n", 
                $expectedArray = ['one', 'two']
            ]           
        ];   
    }
    
}