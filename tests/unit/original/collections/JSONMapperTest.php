<?php

namespace CouponURLs\tests\original\collections;

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Collections\MappedObject;
use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\tests\unit\original\collections\Implementations\ConcreteEmptyMappable;
use CouponURLs\tests\unit\original\collections\Implementations\ConcreteKeysMappable;
use CouponURLs\tests\unit\original\collections\Implementations\ConcreteMappable;
use CouponURLs\Original\Characters\StringManager;
use CouponURLs\Original\Collections\JSONMapper;
use CouponURLs\Original\Collections\Mapper\Types;

Class JSONMapperTest extends \WP_UnitTestcase
{
    /**
     * @dataProvider jsonToArrayProvider
     */
    public function test_returns_array_from_json($JSONString, $expectedArray)
    {
        $result = JSONMapper::getArrayFromJson($JSONString);

        $this->assertIsArray($result);
        $this->assertSame($expectedArray, $result);
    }

    public function test_returns_empty_object()
    {
        (object) $jsonMapper = new JSONMapper([]);

        $this->assertInstanceOf(MappedObject::Class, $jsonMapper->map(""));
        $this->assertEquals([], get_object_vars($jsonMapper->map("")));
    }

    public function test_returns_empty_object_on_malformed_json()
    {
        (object) $jsonMapper = new JSONMapper([]);

        $mapped = $jsonMapper->map("{, ofof");

        $this->assertInstanceOf(MappedObject::Class, $mapped);
        $this->assertEquals([], get_object_vars($mapped));
    }

    public function test_returns_object_with_default_property()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'expectedProperty' => Types::STRING
            ]
        );

        (array) $jsonStrings = [
            "{}",
            "",
            "{noon",
            "[]",
        ];

        (array) $expectedMapped = [
            'expectedProperty' => new StringManager(''),
        ];

        foreach ($jsonStrings as $jsonString) {
            $mapped = $jsonMapper->map($jsonString);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals($expectedMapped, $mapped->asArray());
        }
    }

    public function test_returns_object_with_default_property_overwritten()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'expectedProperty' => Types::STRING
            ]
        );

        $mapped = $jsonMapper->map('{"expectedProperty": "with value"}');

        $this->assertInstanceOf(MappedObject::Class, $mapped);
        $this->assertEquals(
            [
                'expectedProperty' => new StringManager('with value'),            ],
            $mapped->asArray()
        );

    }

    public function test_returns_object_without_properies_not_defined_in_map()
    {
        (array) $map = [
            'onlyPropertyExpected' => Types::STRING
        ];

        (object) $jsonMapper = new JSONMapper(
            $map
        );

        $mapped = $jsonMapper->map('{
            "onlyPropertyExpected": "with value",
            "anotherProperty": "will not make it",
            "thirdProperty": "neither will this"
        }');

        $this->assertInstanceOf(MappedObject::Class, $mapped);
        $this->assertEquals(
            [
                "onlyPropertyExpected"=> new StringManager("with value"),            ],
            $mapped->asArray()
        );

    }

    public function test_returns_object_with_value_string_type_casted()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'expectedProperty' => Types::STRING
            ]
        );

        (array) $jsonDefinitions = [
            [
                'definition' => '{"expectedProperty": 1}',
                'expectedValue' => '1'
            ],
            [
                'definition' => '{"expectedProperty": [3, 2, 1]}',
                'expectedValue' => ''
            ],
            [
                'definition' => '{"expectedProperty": {}}',
                'expectedValue' => ''
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['definition']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(
                [
                    "expectedProperty"=> new StringManager($json['expectedValue']),
                ],
                $mapped->asArray()
            );
        }

    }

    public function test_returns_object_with_value_integer_type_casted()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'expectedProperty' => Types::INTEGER
            ]
        );

        (array) $jsonDefinitions = [
            [
                'definition' => '{"expectedProperty": ""}',
                'expectedValue' => 0
            ],
            [
                'definition' => '{"expectedProperty": "1"}',
                'expectedValue' => 1
            ],
            [
                'definition' => '{"expectedProperty": [3, 2, 1]}',
                'expectedValue' => 0
            ],
            [
                'definition' => '{"expectedProperty": {}}',
                'expectedValue' => 0
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['definition']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(
                [
                    'expectedProperty' => $json['expectedValue'],
                ],
                $mapped->asArray()
            );
        }

    }

    public function test_returns_object_with_empty_collection()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'groupOfItems' => Collection::class
            ]
        );

        (array) $jsonDefinitions = [
            '{}',
            '{"groupOfItems": ""}',
            '{"groupOfItems": []}',
            '{"groupOfItems": {}}',
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->groupOfItems));
            $this->assertInstanceOf(Collection::class, $mapped->groupOfItems);
            $this->assertEmpty($mapped->groupOfItems->asArray());
        }
    }

    public function test_returns_object_with_collection_from_array()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'groupOfItems' => Collection::class
            ]
        );

        (array) $jsonDefinitions = [
            '{"groupOfItems": ["one", "two"]}',
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->groupOfItems));
            $this->assertInstanceOf(Collection::class, $mapped->groupOfItems);
            $this->assertEquals(['one', 'two'], $mapped->groupOfItems->asArray());
        }
    }

    public function test_returns_object_with_collection_from_string()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'groupOfItems' => Collection::class
            ]
        );

        (array) $jsonDefinitions = [
            '{"groupOfItems": "one, two"}' => ['one', 'two'],
            '{"groupOfItems": "one,two"}' => ['one', 'two'],
            '{"groupOfItems": "  one  , two    "}' => ['one', 'two'],
            '{"groupOfItems": "one"}' => ['one'],
        ];

        foreach($jsonDefinitions as $json => $expectedValue) {
            $mapped = $jsonMapper->map($json);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->groupOfItems));
            $this->assertInstanceOf(Collection::class, $mapped->groupOfItems);
            $this->assertEquals($expectedValue, $mapped->groupOfItems->asArray());
        }
    }

    /**
    * @dataProvider CustomSeparatorProvider
    */
    public function test_imports_and_exports_using_a_custom_separator($separator, $valuesToMap, $expectedValue, $expectedExportedString)
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'value' => TYPES::COLLECTION()->separatedBy($separator)
            ]
        );

        $mapped = $jsonMapper->smartMap($valuesToMap);

        $this->assertInstanceOf(MappedObject::Class, $mapped);
        $this->assertEquals(1, count(get_object_vars($mapped)));

        $this->assertTrue(isset($mapped->value));
        $this->assertInstanceOf(Collection::class, $mapped->value);
        $this->assertSame($expectedValue, $mapped->value->asArray());
        $this->assertSame($expectedExportedString, (string) $mapped->value->asStringRepresentation());
    }

    public function test_returns_collection_default_value_when_value_is_not_allowed()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'collectionValue' => Types::COLLECTION()->withDefault(['one', 'two'])
                                                    ->allowed(['one', 'two', 'three'])
            ]
        );

        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": "two"}',
                'value' => new Collection(['two'])
            ],
            [
                'json' => '{"collectionValue": "one,two, three , four"}', // more than the allowed values...
                'value' => new Collection(['one', 'two', 'three'])
            ],
            [
                'json' => '{"collectionValue": ["one","two", "three" , "four"]}', // more than the allowed values (as array)...
                'value' => new Collection(['one', 'two', 'three'])
            ],
            [
                'json' => '{"collectionValue": "one, four, six, two"}', // only some of them allowed...
                'value' => new Collection([0 => 'one', 3 => 'two'])
            ],
            [
                'json' => '{"collectionValue": ["one","four", "six" , "two"]}', // only some of them allowed (as array)...
                'value' => new Collection([0 => 'one', 3 => 'two'])
            ],
            [
                'json' => '{"collectionValue": ""}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": 17}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": 0}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": false}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": true}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": {"nested": "yes"}}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": [1, 2, 3, 4]}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": ["three"]}',
                'value' => new Collection(['three'])
            ],
            [
                'json' => '{"collectionValue": ["three", "one"]}',
                'value' => new Collection(['three', 'one'])
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->collectionValue), $json['json']);
            $this->assertEquals($json['value'], $mapped->collectionValue, $json['json']);
        }
    }

    public function test_returns_default_OR_EMPTY_VALUE_IF_GIVEN_VALUE_IS_AN_ARRAY()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'collectionValue' => Types::COLLECTION()->withDefault(['one', 'two'])
                                                    ->allowEmptyValue(true)
                                                    ->allowed(['one', 'two', 'three'])
            ]
        );

        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                // empty array, valid since it allows an empty value.
                'json' => '{"collectionValue": []}',
                'value' => new Collection([])
            ],
            [
                'json' => '{"collectionValue": "two"}',
                'value' => new Collection(['two'])
            ],
            [
                'json' => '{"collectionValue": "one,two, three , four"}', // more than the allowed values...
                'value' => new Collection(['one', 'two', 'three'])
            ],
            [
                'json' => '{"collectionValue": ["one","two", "three" , "four"]}', // more than the allowed values (as array)...
                'value' => new Collection(['one', 'two', 'three'])
            ],
            [
                'json' => '{"collectionValue": "one, four, six, two"}', // only some of them allowed...
                'value' => new Collection([0 => 'one', 3 => 'two'])
            ],
            [
                'json' => '{"collectionValue": ["one","four", "six" , "two"]}', // only some of them allowed (as array)...
                'value' => new Collection([0 => 'one', 3 => 'two'])
            ],
            [
                'json' => '{"collectionValue": ""}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": 17}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": 0}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": false}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": true}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": {"nested": "yes"}}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": [1, 2, 3, 4]}',
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": ["three"]}',
                'value' => new Collection(['three'])
            ],
            [
                'json' => '{"collectionValue": ["three", "one"]}',
                'value' => new Collection(['three', 'one'])
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->collectionValue), $json['json']);
            $this->assertEquals($json['value'], $mapped->collectionValue, $json['json']);
        }
    }

    public function test_returns_collection_allowed_values_no_defaults()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'collectionValue' => Types::COLLECTION()->allowed(['one', 'two'])
            ]
        );

        // empty string
        // more than the allowed values
        // some of the allowed values
        // incoorrect values like int arrays, null, etc
        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'value' => new Collection([])
            ],
            [
                'json' => '{"collectionValue": "two"}',
                'value' => new Collection(['two'])
            ],
            [
                'json' => '{"collectionValue": "one,two, three , four"}', // more than the allowed values...
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": ["one","two", "three" , "four"]}', // more than the allowed values (as array)...
                'value' => new Collection(['one', 'two'])
            ],
            [
                'json' => '{"collectionValue": "one, four, six, two"}', // only some of them allowed...
                'value' => new Collection([0 => 'one', 3 => 'two'])
            ],
            [
                'json' => '{"collectionValue": ["one","four", "six" , "two"]}', // only some of them allowed (as array)...
                'value' => new Collection([0 => 'one', 3 => 'two'])
            ],
            [
                'json' => '{"collectionValue": ""}',
                'value' => new Collection([])
            ],
            [
                'json' => '{"collectionValue": 17}',
                'value' => new Collection([])
            ],
            [
                'json' => '{"collectionValue": 0}',
                'value' => new Collection([])
            ],
            [
                'json' => '{"collectionValue": false}',
                'value' => new Collection([])
            ],
            [
                'json' => '{"collectionValue": true}',
                'value' => new Collection([])
            ],
            [
                'json' => '{"collectionValue": {"nested": "yes"}}',
                'value' => new Collection([])
            ],
            [
                'json' => '{"collectionValue": [1, 2, 3, 4]}',
                'value' => new Collection([])
            ],
            [
                'json' => '{"collectionValue": ["three"]}',
                'value' => new Collection([])
            ],
            [
                'json' => '{"collectionValue": ["three", "one"]}',
                'value' => new Collection([1 =>'one'])
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->collectionValue), $json['json']);
            $this->assertEquals($json['value'], $mapped->collectionValue, $json['json']);
        }
    }

    public function test_returns_object_with_collection_from_integer()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'groupOfItems' => Collection::class
            ]
        );

        (array) $jsonDefinitions = [
            '{"groupOfItems": 4}',
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->groupOfItems));
            $this->assertInstanceOf(Collection::class, $mapped->groupOfItems);
            $this->assertEquals([4], $mapped->groupOfItems->asArray());
        }
    }

    public function test_returns_string_property()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'stringValue' => Types::STRING
            ]
        );

        (array) $jsonDefinitions = [
            '{}',
            '{"stringValue": "a string"}',
            '{"stringValue": ""}',
            '{"stringValue": 17}',
            '{"stringValue": 0}',
            '{"stringValue": false}',
            '{"stringValue": true}',
            '{"stringValue": {"nested": "yes"}}',
            '{"stringValue": [1, 2, 3, 4]}',
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->stringValue));
            $this->assertInstanceof(StringManager::class, $mapped->stringValue);
        }
    }

    public function test_returns_string_default_value()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'stringValue' => Types::STRING()->withDefault('the default string')
            ]
        );

        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'value' => 'the default string'
            ],
            [
                'json' => '{"stringValue": "a string"}',
                'value' => 'a string'
            ],
            [
                'json' => '{"stringValue": ""}',
                'value' => 'the default string'
            ],
            [
                'json' => '{"stringValue": 17}',
                'value' => 'the default string'
            ],
            [
                'json' => '{"stringValue": 0}',
                'value' => 'the default string'
            ],
            [
                'json' => '{"stringValue": false}',
                'value' => 'the default string'
            ],
            [
                'json' => '{"stringValue": true}',
                'value' => 'the default string'
            ],
            [
                'json' => '{"stringValue": {"nested": "yes"}}',
                'value' => 'the default string'
            ],
            [
                'json' => '{"stringValue": [1, 2, 3, 4]}',
                'value' => 'the default string'
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->stringValue));
            $this->assertInstanceOf(StringManager::class, $mapped->stringValue);
            $this->assertEquals($json['value'], $mapped->stringValue);
        }
    }

    public function test_returns_string_default_value_when_value_is_not_allowed()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'stringValue' => Types::STRING()->withDefault('the default string')
                                                ->allowed(['one', 'two', 'the default string'])
            ]
        );

        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'value' => 'the default string'
            ],
            [
                'json' => '{"stringValue": "two"}',
                'value' => 'two'
            ],
            [
                'json' => '{"stringValue": ""}',
                'value' => 'the default string'
            ],
            [
                'json' => '{"stringValue": 17}',
                'value' => 'the default string'
            ],
            [
                'json' => '{"stringValue": 0}',
                'value' => 'the default string'
            ],
            [
                'json' => '{"stringValue": false}',
                'value' => 'the default string'
            ],
            [
                'json' => '{"stringValue": true}',
                'value' => 'the default string'
            ],
            [
                'json' => '{"stringValue": {"nested": "yes"}}',
                'value' => 'the default string'
            ],
            [
                'json' => '{"stringValue": [1, 2, 3, 4]}',
                'value' => 'the default string'
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->stringValue));
            $this->assertInstanceOf(StringManager::class, $mapped->stringValue);
            $this->assertEquals($json['value'], $mapped->stringValue);
        }
    }

    public function test_returns_string_when_value_is_not_allowed_and_default_has_not_been_set_should_return_the_first_allowed_value()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'stringValue' => Types::STRING()->allowed(['one', 'two', 'the default string'])
            ]
        );

        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'value' => 'one'
            ],
            [
                'json' => '{"stringValue": "two"}',
                'value' => 'two'
            ],
            [
                'json' => '{"stringValue": ""}',
                'value' => 'one'
            ],
            [
                'json' => '{"stringValue": 17}',
                'value' => 'one'
            ],
            [
                'json' => '{"stringValue": 0}',
                'value' => 'one'
            ],
            [
                'json' => '{"stringValue": false}',
                'value' => 'one'
            ],
            [
                'json' => '{"stringValue": true}',
                'value' => 'one'
            ],
            [
                'json' => '{"stringValue": {"nested": "yes"}}',
                'value' => 'one'
            ],
            [
                'json' => '{"stringValue": [1, 2, 3, 4]}',
                'value' => 'one'
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->stringValue));
            $this->assertInstanceOf(StringManager::class, $mapped->stringValue);
            $this->assertEquals($json['value'], $mapped->stringValue);
        }
    }

    public function test_returns_integer_property()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'integerValue' => Types::INTEGER
            ]
        );

        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'expectedValue' => 0
            ],
            [
                'json' => '{"integerValue": "a string"}',
                'expectedValue' => 0
            ],
            [
                'json' => '{"integerValue": ""}',
                'expectedValue' => 0
            ],
            [
                'json' => '{"integerValue": 17}',
                'expectedValue' => 17
            ],
            [
                'json' => '{"integerValue": "17"}',
                'expectedValue' => 17
            ],
            [
                'json' => '{"integerValue": 0}',
                'expectedValue' => 0
            ],
            [
                'json' => '{"integerValue": false}',
                'expectedValue' => 0
            ],
            [
                'json' => '{"integerValue": true}',
                'expectedValue' => 0
            ],
            [
                'json' => '{"integerValue": {"nested": "yes"}}',
                'expectedValue' => 0
            ],
            [
                'json' => '{"integerValue": [1, 2, 3, 4]}',
                'expectedValue' => 0
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->integerValue));
            $this->assertIsInt( $mapped->integerValue);
            $this->assertSame($json['expectedValue'], $mapped->integerValue);
        }
    }

    public function test_returns_integer_default_value()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'integerValue' => Types::INTEGER()->withDefault(10)
            ]
        );

        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": "a string"}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": ""}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": 17}',
                'value' => 17
            ],
            [
                'json' => '{"integerValue": 0}',
                'value' => 0
            ],
            [
                'json' => '{"integerValue": -1}',
                'value' => -1
            ],
            [
                'json' => '{"integerValue": false}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": true}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": {"nested": "yes"}}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": [1, 2, 3, 4]}',
                'value' => 10
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->integerValue));
            $this->assertIsInt( $mapped->integerValue);
            $this->assertEquals($json['value'], $mapped->integerValue);
        }
    }

    public function test_returns_integer_minimum()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'integerValue' => Types::INTEGER()->minimum(10)
            ]
        );

        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": "a string"}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": ""}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": 17}',
                'value' => 17
            ],
            [
                'json' => '{"integerValue": 0}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": -1}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": false}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": true}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": {"nested": "yes"}}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": [1, 2, 3, 4]}',
                'value' => 10
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->integerValue));
            $this->assertIsInt( $mapped->integerValue);
            $this->assertEquals($json['value'], $mapped->integerValue);
        }
    }

    public function test_returns_integer_minimum_or_default_if_the_later_is_higher()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'integerValue' => Types::INTEGER()->minimum(10)->withDefault(12)
            ]
        );

        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'value' => 12
            ],
            [
                'json' => '{"integerValue": "a string"}',
                'value' => 12
            ],
            [
                'json' => '{"integerValue": ""}',
                'value' => 12
            ],
            [
                'json' => '{"integerValue": 17}',
                'value' => 17
            ],
            [
                'json' => '{"integerValue": 10}',
                'value' => 10
            ],
            [
                'json' => '{"integerValue": 11}',
                'value' => 11
            ],
            [
                'json' => '{"integerValue": 0}',
                'value' => 12
            ],
            [
                'json' => '{"integerValue": -1}',
                'value' => 12
            ],
            [
                'json' => '{"integerValue": false}',
                'value' => 12
            ],
            [
                'json' => '{"integerValue": true}',
                'value' => 12
            ],
            [
                'json' => '{"integerValue": {"nested": "yes"}}',
                'value' => 12
            ],
            [
                'json' => '{"integerValue": [1, 2, 3, 4]}',
                'value' => 12
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->integerValue));
            $this->assertIsInt( $mapped->integerValue);
            $this->assertEquals($json['value'], $mapped->integerValue, $json['json']);
        }
    }

    public function test_returns_boolean_property()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'booleanValue' => Types::BOOLEAN
            ]
        );

        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": "a string"}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": ""}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": 17}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": 1}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": "1"}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": 0}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": false}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": true}',
                'value' => true
            ],
            [
                'json' => '{"booleanValue": "true"}',
                'value' => true
            ],
            [
                'json' => '{"booleanValue": "false"}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": {"nested": "yes"}}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": [1, 2, 3, 4]}',
                'value' => false
            ],
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->booleanValue));
            $this->assertIsBool( $mapped->booleanValue, "json: {$json['json']}");
            $this->assertEquals($json['value'], $mapped->booleanValue, "json: {$json['json']}");
        }
    }

    public function test_returns_boolean_default_value()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'booleanValue' => Types::BOOLEAN()->withDefault(false)
            ]
        );

        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": "a string"}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": ""}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": 17}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": 0}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": -1}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": false}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": true}',
                'value' => true
            ],
            [
                'json' => '{"booleanValue": {"nested": "yes"}}',
                'value' => false
            ],
            [
                'json' => '{"booleanValue": [1, 2, 3, 4]}',
                'value' => false
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->booleanValue));
            $this->assertIsBool( $mapped->booleanValue);
            $this->assertEquals($json['value'], $mapped->booleanValue);
        }
    }

    public function test_returns_raw_type__doesnt_type_cast_it()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'integerValue' => Types::ANY
            ]
        );

        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'expectedValue' => ''
            ],
            [
                'json' => '{"integerValue": "a string"}',
                'expectedValue' => "a string"
            ],
            [
                'json' => '{"integerValue": ""}',
                'expectedValue' => ""
            ],
            [
                'json' => '{"integerValue": 17}',
                'expectedValue' => 17
            ],
            [
                'json' => '{"integerValue": "17"}',
                'expectedValue' => "17"
            ],
            [
                'json' => '{"integerValue": 0}',
                'expectedValue' => 0
            ],
            [
                'json' => '{"integerValue": false}',
                'expectedValue' => false
            ],
            [
                'json' => '{"integerValue": true}',
                'expectedValue' => true
            ],
            [
                'json' => '{"integerValue": {"nested": "yes"}}',
                'expectedValue' => (object) ['nested' => 'yes']
            ],
            [
                'json' => '{"integerValue": [1, 2, 3, 4]}',
                'expectedValue' => [1, 2, 3, 4]
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->integerValue));
            
            if (is_object($mapped->integerValue)) {
                $this->assertEquals($json['expectedValue'], $mapped->integerValue);
            } else {
                $this->assertSame($json['expectedValue'], $mapped->integerValue);
            }
            
        }
    }

    public function test_returns_COLLECTION_default_value()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'collectionValue' => Types::COLLECTION()->withDefault([1, 2, 3, 4])
            ]
        );

        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'value' => [1, 2, 3, 4]
            ],
            [
                'json' => '{"collectionValue": "a string"}',
                'value' => ['a string']
            ],
            [
                'json' => '{"collectionValue": ""}',
                'value' => [1, 2, 3, 4]
            ],
            [
                'json' => '{"collectionValue": 17}',
                'value' => [1, 2, 3, 4]
            ],
            [
                'json' => '{"collectionValue": 0}',
                'value' => [1, 2, 3, 4]
            ],
            [
                'json' => '{"collectionValue": false}',
                'value' => [1, 2, 3, 4]
            ],
            [
                'json' => '{"collectionValue": true}',
                'value' => [1, 2, 3, 4]
            ],
            [
                'json' => '{"collectionValue": {"nested": "yes"}}',
                'value' => [1, 2, 3, 4]
            ],
            [
                'json' => '{"collectionValue": [6, 7, 8, 9]}',
                'value' => [6, 7, 8, 9]
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);

            $this->assertInstanceOf(MappedObject::Class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->collectionValue));
            $this->assertInstanceOf(Collection::class, $mapped->collectionValue);
            $this->assertEquals($json['value'], $mapped->collectionValue->asArray(), $json['json']);
        }
    }

    public function test_maps_from_object()
    {
        (array) $map = [
            'expectedProperty' => "the value"
        ];

        (object) $jsonMapper = new JSONMapper(
            $map
        );

        (object) $objectAsSource = (object) $map;

        $mapped = $jsonMapper->smartMap($objectAsSource);

        $this->assertInstanceOf(MappedObject::Class, $mapped);
        $this->assertEquals(1, count(get_object_vars($mapped)));
        $this->assertTrue(isset($mapped->expectedProperty));
        $this->assertEquals('the value', $mapped->expectedProperty);
    }

    public function _test_returns_value_when_mappable_object_has_no_map()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'concreteMappable' => ConcreteEmptyMappable::class
            ]
        );

        (object) $mappedObject = new MappedObject;

        $mappedObject->setMappedFieldsFound(new MappedObject);
        $mappedObject->setRawDataFound(new Collection([]));

        (object) $mappedObject2 = clone $mappedObject;
        
        $mappedObject->allFieldsFoundInSource->push('nested');

        (array) $jsonDefinitions = [
            [
                'json' => '{}',
                'value' => ''
            ],
            [
                'json' => '{"concreteMappable": "a string"}',
                'value' => "a string"
            ],
            [
                'json' => '{"concreteMappable": ""}',
                'value' => ""
            ],
            [
                'json' => '{"concreteMappable": 17}',
                'value' => 17
            ],
            [
                'json' => '{"concreteMappable": 0}',
                'value' => 0
            ],
            [
                'json' => '{"concreteMappable": false}',
                'value' => false
            ],
            [
                'json' => '{"concreteMappable": true}',
                'value' => true
            ],
            [
                'json' => '{"concreteMappable": {"nested": "yes"}}',
                'value' => $mappedObject
            ],
            [
                'json' => '{"concreteMappable": [1, 2, 3, 4]}',
                'value' => $mappedObject2
            ]
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);
            $this->assertInstanceOf(MappedObject::class, $mapped);
            $this->assertEquals(1, count(get_object_vars($mapped)));

            $this->assertTrue(isset($mapped->concreteMappable));
            $this->assertInstanceOf(ConcreteEmptyMappable::class, $mapped->concreteMappable);
            $this->assertEquals($json['value'], $mapped->concreteMappable->data);
        }
    }

    public function test_returns_values_only_available_on_map_everything_else_ignored()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'concreteMappable' => ConcreteKeysMappable::class
            ]
        );

        (array) $jsonDefinitions = [
            [
                'json' => '{"concreteMappable": {"onlyStringsExpected": "with a string value"}}',
                'value' => "with a string value"
            ],
            [
                'json' => '{}',
                'value' => ''
            ],
            [
                'json' => '{"concreteMappable": "a string"}',
                'value' => ""
            ],
            [
                'json' => '{"concreteMappable": ""}',
                'value' => ""
            ],
            [
                'json' => '{"concreteMappable": 17}',
                'value' => ""
            ],
            [
                'json' => '{"concreteMappable": 0}',
                'value' => ""
            ],
            [
                'json' => '{"concreteMappable": false}',
                'value' => ""
            ],
            [
                'json' => '{"concreteMappable": true}',
                'value' => ""
            ],
            [
                'json' => '{"concreteMappable": {"nested": "yes"}}',
                'value' => ""
            ],
            [
                'json' => '{"concreteMappable": [1, 2, 3, 4]}',
                'value' => ""
            ],
        ];

        foreach($jsonDefinitions as $json) {
            $mapped = $jsonMapper->map($json['json']);
            $this->assertInstanceOf(MappedObject::class, $mapped);
            $this->assertInstanceOf(ConcreteKeysMappable::class, $mapped->concreteMappable);

            $this->assertEquals(1, count(get_object_vars($mapped->concreteMappable->data)));

            $this->assertTrue(isset($mapped->concreteMappable->data->onlyStringsExpected));
            $this->assertInstanceOf(StringManager::class, $mapped->concreteMappable->data->onlyStringsExpected);
            $this->assertEquals($json['value'], $mapped->concreteMappable->data->onlyStringsExpected);
        }
    }

    /**
    * @dataProvider recursiveMapProvider
    */
    public function test_maps_recursively($json, $expectedId, $expectedName, $expectedIsActive)
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'data' => [
                    'id' => Types::INTEGER,
                    'name' => Types::STRING()->withDefault('the name'),
                    'isActive' => Types::BOOLEAN
                ]
            ]
        );

        (object) $mapped = $jsonMapper->map($json);

        $this->assertInstanceof(MappedObject::class, $mapped);

        $this->assertTrue(isset($mapped->data));
        $this->assertInstanceof(MappedObject::class, $mapped->data);

        $this->assertTrue(isset($mapped->data->id));
        $this->assertTrue(isset($mapped->data->name));
        $this->assertTrue(isset($mapped->data->isActive));

        $this->assertIsInt( $mapped->data->id);
        $this->assertInstanceof(StringManager::class, $mapped->data->name);
        $this->assertIsBool( $mapped->data->isActive);

        $this->assertSame($expectedId, $mapped->data->id);
        $this->assertSame($expectedName, $mapped->data->name->get());
        $this->assertSame($expectedIsActive, $mapped->data->isActive);
    }

    /**
    * @dataProvider recursiveMultiLevelProvider
    */
    public function test_maps_recursively_multilevel($json, $expectedCount, $expectedAllowUnactives,$expectedIds)
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'data' => [
                    'friends' => [
                        'active' => [
                            'count' => Types::INTEGER()->withDefault(9)
                        ],
                        'unactive' => [
                            'allowUnactives' => TYPES::BOOLEAN,
                            'ids' => TYPES::COLLECTION()
                        ]
                    ],
                    'name' => Types::STRING()->withDefault('the name'),
                    'isActive' => Types::BOOLEAN
                ]
            ]
        );

        (object) $mapped = $jsonMapper->map($json);

        $this->assertInstanceof(MappedObject::class, $mapped);

        $this->assertTrue(isset($mapped->data));
        $this->assertInstanceof(MappedObject::class, $mapped->data);

        $this->assertTrue(isset($mapped->data->friends));
        $this->assertTrue(isset($mapped->data->name));
        $this->assertTrue(isset($mapped->data->isActive));

        $this->assertInstanceof(MappedObject::class, $mapped->data->friends);
        $this->assertInstanceof(StringManager::class, $mapped->data->name);
        $this->assertIsBool( $mapped->data->isActive);

        $this->assertTrue(isset($mapped->data->friends->active));
        $this->assertTrue(isset($mapped->data->friends->unactive));

        $this->assertInstanceof(MappedObject::class, $mapped->data->friends->active);
        $this->assertInstanceof(MappedObject::class, $mapped->data->friends->unactive);

        $this->assertTrue(isset($mapped->data->friends->active->count));

        $this->assertTrue(isset($mapped->data->friends->unactive->allowUnactives));
        $this->assertTrue(isset($mapped->data->friends->unactive->ids));


        $this->assertIsInt( $mapped->data->friends->active->count);
        $this->assertIsBool( $mapped->data->friends->unactive->allowUnactives);
        $this->assertInstanceof(Collection::class, $mapped->data->friends->unactive->ids);

        $this->assertSame($expectedCount, $mapped->data->friends->active->count);
        $this->assertSame($expectedAllowUnactives, $mapped->data->friends->unactive->allowUnactives);
        $this->assertSame($expectedIds, $mapped->data->friends->unactive->ids->asArray());

        $this->assertSame('the name', $mapped->data->name->get());
        $this->assertSame(false, $mapped->data->isActive);
    }

    public function test_maps_field_with_string_manager_value__which_is_an_object_implementing_jsonSerializable()
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'propertyWithStringManager' => Types::STRING()->withDefault('the default string')
            ]
        );

        (array) $arrayOfValues = [
            'propertyWithStringManager' =>  new StringManager('the new value')
        ];

        (object) $mapped = $jsonMapper->smartMap($arrayOfValues);

        $this->assertInstanceOf(MappedObject::Class, $mapped);
        $this->assertTrue(isset($mapped->propertyWithStringManager));
        $this->assertInstanceof(StringManager::class, $mapped->propertyWithStringManager);
        $this->assertEquals('the new value', (string) $mapped->propertyWithStringManager);
    }

    /**
    * @dataProvider filedsNotFoundProvider
    */
    public function test_returns_filed_names_not_found_in_data_source($json, $fieldsNotFound)
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'id' => Types::INTEGER,
                'name' => Types::STRING,
                'isEnabled' => Types::BOOLEAN
            ]
        );

        (object) $mapped = $jsonMapper->map($json);

        $this->assertInstanceOf(Collection::class, $mapped->mapFieldsNotFoundInSource);
        $this->assertEquals($fieldsNotFound, $mapped->mapFieldsNotFoundInSource->asArray());
        $this->assertContainsOnlyInstancesOf(StringManager::class, $mapped->mapFieldsNotFoundInSource->asArray()); 
    }

    /**
    * @dataProvider fieldsFoundProvider
    */
    public function test_returns_filed_names_found_in_data_source($json, $fieldsFound)
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'id' => Types::INTEGER,
                'name' => Types::STRING,
                'isEnabled' => Types::BOOLEAN
            ]
        );

        (object) $mapped = $jsonMapper->map($json);

        $this->assertInstanceOf(Collection::class, $mapped->mapFieldsFoundInSource);
        $this->assertEquals($fieldsFound, $mapped->mapFieldsFoundInSource->asArray());
        $this->assertContainsOnlyInstancesOf(StringManager::class, $mapped->mapFieldsFoundInSource->asArray()); 
    }

    /**
    * @dataProvider mappedFieldsFoundProvider
    */
    public function test_returns_mapped_object_only_with_fields_found_in_data_source($json, $mappedObject)
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'id' => Types::INTEGER,
                'name' => Types::STRING,
                'isEnabled' => Types::BOOLEAN
            ]
        );

        (object) $mapped = $jsonMapper->map($json);

        $this->assertInstanceOf(MappedObject::class, $mapped->dataFound);
        $this->assertEquals($mappedObject, $mapped->dataFound);
    }

    /**
    * @dataProvider rawMappedFieldsFoundProvider
    */
    public function test_returns_mapped_object_with_raw_data_found_as_a_collection_object($json, $mappedObject)
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'id' => Types::INTEGER,
                'name' => Types::STRING,
                'isEnabled' => Types::BOOLEAN
            ]
        );

        (object) $mapped = $jsonMapper->map($json);

        $this->assertInstanceOf(Collection::class, $mapped->rawDataFound);
        $this->assertEquals($mappedObject, $mapped->rawDataFound);
    }
    
    /**
    * @dataProvider alfieldsFoundProvider
    */
    public function test_returns_all_fields_found_in_data_source_even_the_ones_not_in_map($json, $fieldsFound)
    {
        (object) $jsonMapper = new JSONMapper(
            $map = [
                'id' => Types::INTEGER,
                'name' => Types::STRING,
                'isEnabled' => Types::BOOLEAN
            ]
        );

        (object) $mapped = $jsonMapper->map($json);

        $this->assertInstanceOf(Collection::class, $mapped->allFieldsFoundInSource);
        $this->assertEquals($fieldsFound, $mapped->allFieldsFoundInSource->asArray());
        $this->assertContainsOnlyInstancesOf(StringManager::class, $mapped->allFieldsFoundInSource->asArray()); 
    }
    
    public function alfieldsFoundProvider()
    {
        return [
            'all found' => [
                $json = '{
                    "id": 3,
                    "name": "rafael",
                    "isEnabled": true
                }',
                $mappedObject = [
                    "id",
                    "name",
                    "isEnabled",
                ]
            ],
            'all in map plus some other not expected' => [
                $json = '{
                    "id": 3,
                    "name": "rafael",
                    "isEnabled": true,
                    "delete_site": true,
                    "send_passwords_before_doing_that": "yes"
                }',
                $mappedObject = [
                    "id",
                    "name",
                    "isEnabled",
                    "delete_site",
                    "send_passwords_before_doing_that"
                ]
            ],
            'all found, fields in different order than in map' => [
                $json = '{
                    "isEnabled": true,
                    "name": "rafael",
                    "id": 3
                }',
                $mappedObject = [
                    "isEnabled",
                    "name",
                    "id",
                ]
            ],
            'id not found' => [
                $json = '{
                    "name": "rafael",
                    "isEnabled": true
                }',
                $mappedObject = [
                    "name",
                    "isEnabled",
                ]
            ],
            'id and isenabled not found' => [
                $json = '{
                    "name": "rafael"
                }',
                $mappedObject = [
                    "name"
                ]
            ],
            'only fields not in map' => [
                $json = '{
                    "delete_site": true,
                    "send_passwords_before_doing_that": "yes"
                }',
                $mappedObject = [
                    "delete_site",
                    "send_passwords_before_doing_that"
                ]
            ],
            'none found' => [
                $json = '{
                }',
                $mappedObject = [
                ]
            ],
        ];    
    }
    
    public function mappedFieldsFoundProvider()
    {
        return [
            'all found' => [
                $json = '{
                    "id": 3,
                    "name": "rafael",
                    "isEnabled": true
                }',
                $mappedObject = new MappedObject([
                    "id"=> 3,
                    "name"=> "rafael",
                    "isEnabled"=> true
                ])
            ],
            'all found, fields in different order than in map' => [
                $json = '{
                    "isEnabled": true,
                    "name": "rafael",
                    "id": 3
                }',
                $mappedObject = new MappedObject ([
                    "id"=> 3,
                    "name"=> "rafael",
                    "isEnabled"=> true
                ])
            ],
            'id not found' => [
                $json = '{
                    "name": "rafael",
                    "isEnabled": true
                }',
                $mappedObject = new MappedObject ([
                    "name"=> "rafael",
                    "isEnabled"=> true
                ])
            ],
            'id and isenabled not found' => [
                $json = '{
                    "name": "rafael"
                }',
                $mappedObject = new mappedObject ([
                    "name"=> "rafael",
                ])
            ],
            'none found' => [
                $json = '{
                }',
                $mappedObject = new mappedObject ([
                ])
            ],
        ];   
    }

    public function rawMappedFieldsFoundProvider()
    {
        return [
            'all found' => [
                $json = '{
                    "id": 3,
                    "name": "rafael",
                    "isEnabled": true
                }',
                $mappedObject = new Collection([
                    "id"=> 3,
                    "name"=> "rafael",
                    "isEnabled"=> true
                ])
            ],
            'all found, fields in different order than in map' => [
                $json = '{
                    "isEnabled": true,
                    "name": "rafael",
                    "id": 3
                }',
                $mappedObject = new Collection([
                    "id"=> 3,
                    "name"=> "rafael",
                    "isEnabled"=> true
                ])
            ],
            'id not found' => [
                $json = '{
                    "name": "rafael",
                    "isEnabled": true
                }',
                $mappedObject = new Collection([
                    "name"=> "rafael",
                    "isEnabled"=> true
                ])
            ],
            'id and isenabled not found' => [
                $json = '{
                    "name": "rafael"
                }',
                $mappedObject = new Collection([
                    "name"=> "rafael",
                ])
            ],
            'none found' => [
                $json = '{
                }',
                $mappedObject = new Collection([])
            ],
           'all found, id is not an integer' => [
                $json = '{
                    "id": "this is a string",
                    "name": "rafael",
                    "isEnabled": true
                }',
                $mappedObject = new Collection([
                    "id"=> "this is a string",
                    "name"=> "rafael",
                    "isEnabled"=> true
                ])
            ],
            'all found, id is not an integer, name is not a string' => [
                $json = '{
                    "id": "this is a string",
                    "name": [764],
                    "isEnabled": true
                }',
                $mappedObject = new Collection([
                    "id"=> "this is a string",
                    "name"=> [764],
                    "isEnabled"=> true
                ])
            ],
            'all found, id is not an integer, name is not a string and isEnabled is not a boolean' => [
                $json = '{
                    "id": "this is a string",
                    "name": [764],
                    "isEnabled": "not a boolean"
                }',
                $mappedObject = new Collection([
                    "id"=> "this is a string",
                    "name"=> [764],
                    "isEnabled"=> 'not a boolean'
                ])
            ],
        ];   
    }

    public function fieldsFoundProvider()
    {
        return [
            'all found' => [
                $json = '{
                    "id": 3,
                    "name": "rafael",
                    "isEnabled": true
                }',
                $fieldsFound = [new StringManager('id'), new StringManager('name'), new StringManager('isEnabled')]
            ],
            'all found, fields in different order than in map' => [
                $json = '{
                    "isEnabled": true,
                    "name": "rafael",
                    "id": 3
                }',
                $fieldsFound = [new StringManager('id'), new StringManager('name'), new StringManager('isEnabled')]
            ],
            'id not found' => [
                $json = '{
                    "name": "rafael",
                    "isEnabled": true
                }',
                $fieldsFound = [new StringManager('name'), new StringManager('isEnabled')]
            ],
            'id and isenabled not found' => [
                $json = '{
                    "name": "rafael"
                }',
                $fieldsFound = [new StringManager('name')]
            ],
            'none found' => [
                $json = '{
                }',
                $fieldsFound = []
            ],
        ];   
    }

    public function filedsNotFoundProvider()
    {
        return [
            'none not found' => [
                $json = '{
                    "id": 3,
                    "name": "rafael",
                    "isEnabled": true
                }',
                $fieldsNotFound = []
            ],
            'none not found, fields in different order than in map' => [
                $json = '{
                    "isEnabled": true,
                    "name": "rafael",
                    "id": 3
                }',
                $fieldsNotFound = []
            ],
            'id not found' => [
                $json = '{
                    "name": "rafael",
                    "isEnabled": true
                }',
                $fieldsNotFound = [new StringManager('id')]
            ],
            'id and isenabled not found' => [
                $json = '{
                    "name": "rafael"
                }',
                $fieldsNotFound = [new StringManager('id'), new StringManager('isEnabled')]
            ],
            'none found' => [
                $json = '{
                }',
                $fieldsNotFound = [new StringManager('id'), new StringManager('name'), new StringManager('isEnabled')]
            ],
        ];   
    }
    
    public function recursiveMultiLevelProvider()
    {
        return [
            'empty json' => [
                $json = '{}',
                $expectedCount = 9,
                $expectedAllowUnactives = false,
                $expectedIds = []
           ],
           'json custom values' => [
                $json = '{
                    "data": {
                        "friends": {
                            "active": {
                                "count": 456
                            },
                            "unactive": {
                                "allowUnactives": true,
                                "ids": [3, 4, 5]
                            }
                        }
                    }
                }',
                $expectedCount = 456,
                $expectedAllowUnactives = true,
                $expectedIds = [3, 4, 5]
           ]
        ];   
    }

    public function recursiveMapProvider()
    {
        return [
            'no values' => [
                $json = '{
                    "data": []
                }',
                $expectedId = 0,
                $expectedName = 'the name',
                $expectedIsActive = false
            ],
            'values replace defaults' => [
                $json = '{
                    "data": {
                        "id": 5,
                        "name": "rafa serna",
                        "isActive": true
                    }
                }',
                $expectedId = 5,
                $expectedName = 'rafa serna',
                $expectedIsActive = true
            ]
        ];   
    }
    public function jsonToArrayProvider()
    {
        return [
            'empty string --> empty array' => [
                $JSONString = '',
                $expectedArray = []
            ],
            'null --> empty array' => [
                $JSONString = '',
                $expectedArray = []
            ],
            'malformed json --> empty array' => [
                $JSONString = 'jso":',
                $expectedArray = []
            ],
            'array --> empty array' => [
                $JSONString = [1, 2, 4],
                $expectedArray = []
            ],
            'object --> empty array' => [
                $JSONString = new \StdClass,
                $expectedArray = []
            ],
            'json array --> array' => [
                $JSONString = '{"numbers": [1, 2, 3]}',
                $expectedArray = ['numbers' => [1, 2, 3]]
            ],
        ];   
    }

    public function CustomSeparatorProvider()
    {
        return [
            'value as array, separator is new line' => [
                $separator = "\n",
                $valuesToMap = [
                    'value' => ['one', 'two']
                ],
                $expectedValue = ['one', 'two'],
                $expectedExportedString = "one\ntwo",
            ],
            'value as string, separator is new line' => [
                $separator = "\n",
                $valuesToMap = [
                    'value' => "one\ntwo"
                ],
                $expectedValue = ['one', 'two'],
                $expectedExportedString = "one\ntwo",
            ]
        ];   
    }
    
}