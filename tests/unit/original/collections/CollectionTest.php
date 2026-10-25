<?php

namespace CouponURLs\tests\original\collections;

use CouponURLs\Original\Abilities\Comparable;
use CouponURLs\Original\Abilities\Invokable;
use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\Original\Characters\StringManager;
use CouponURLs\Original\Collections\Abilities\ArrayRepresentation;
use CouponURLs\Tests\Unit\Original\Collections\Fakes\CheckerFake;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\TestRunner\TestResult\Collector;

use function CouponURLs\Original\Utilities\Collection\a;

Class CollectionTest extends \WP_UnitTestcase
{
    public function test_implements_array_representation()
    {
        (object) $collection = new Collection([]);

        $this->assertInstanceof(ArrayRepresentation::class, $collection);   
    }

    /**
    * @dataProvider lastKeyProvider
    */
    public function test_returns_last_key(Array $elements, $expectedKey)
    {
        (object) $collection = new Collection($elements);

        $this->assertSame($expectedKey, $collection->lastKey());   
    }

    public function test_returns_first_element()
    {
        (object) $collection = new Collection(['one', 'two', 'three']);

        $this->assertEquals('one', $collection->first());   
    }

    public function test_returns_first_element_null_on_empty()
    {
        (object) $collection = new Collection([]);

        $this->assertSame(null, $collection->first());   
    }

    public function test_returns_last_element_null_on_empty()
    {
        (object) $collection = new Collection([]);

        $this->assertSame(null, $collection->last());   
    }

    public function test_returns_indexed_element()
    {
        (object) $collection = new Collection(['one', 'two', 'three', 'four']);

        $this->assertEquals('three', $collection->atPosition(3));   
    }

    /**
    * @dataProvider atPopsitionProvider
    */
    public function test_returns_indexed_element_strings_as_keys($position, $expectedElement)
    {
        (object) $collection = new Collection(['first' => 'one',
                                               'second' => 'two',
                                               'third' => 'three',
                                               'fourth' => 'four']);

        $this->assertEquals($expectedElement, $collection->atPosition($position));   
    }

    public function test_silently_returns_null_on_non_existent()
    {
        (object) $collection = new Collection(['one', 'two', 'three', 'four']);

        $this->assertEquals(null, $collection->atPosition(90));   
    }

    public function test_returns_indexed_array()
    {
        (array) $numbers = ['one', 'two', 'three', 'four'];
        (object) $collection = new Collection($numbers);

        $this->assertEquals($numbers, $collection->asArray());   
    }

    public function test_removes_NO_false_elements()
    {
        (array) $items = ['one', '', 'false', null, 0];
        (object) $collection = new Collection($items);

        $this->assertSame($items, $collection->asArray());   
    }

    public function test_removes_falsey_elements_immutably___that_is__it_returns_a_new_object_and_leaves_the_instance_it_was_called_from_intact()
    {
        (array) $items = ['one', '', false, null, 0, []];
        (array) $cleanItems = ['one'];

        (object) $collection = new Collection($items);
        (object) $cleanCollection = $collection->clean();

        $this->assertSame($items, $collection->asArray());   

        $this->assertNotSame($collection, $cleanCollection);

        $this->assertSame($cleanItems, $cleanCollection->asArray());
    }

    public function test_returns_associative_array()
    {
        (array) $food = [
            'sweet' => 'cake',
            'savory' => 'pizza'
        ];
        
        (object) $collection = new Collection($food);

        $this->assertEquals($food, $collection->asArray());   
    }

    public function test_searches_case_insensitive()
    {
        (object) $collection = new Collection(['one', 'two', 'THrEe']);

        $this->assertTrue($collection->contain('three'));   
    }

    /**
    * @dataProvider callClosurecontainProvider
    */
    public function calls_only_closures($valueToSearch, $expectedResult)
    {
        (object) $collection = new Collection(['trigger_error', 'two', 'THrEe']);

        $this->assertSame($expectedResult, $collection->contain($valueToSearch));   
    }

    /**
    * @dataProvider containAllProvider
    */
    public function test_contains_all_elements($elements, $elementsToCheck, $expectedResult)
    {
        (object) $collection = new Collection($elements);

        $this->assertSame($expectedResult, $collection->containAll($elementsToCheck));
    }
    
    /**
    * @dataProvider containProvider
    */
    public function test_searches_contain($elements, $valueToSearch, $result)
    {
        (object) $collection = new Collection($elements);

        $this->assertSame($result, $collection->contain($valueToSearch));   
    }

    public function test_adds_element()
    {
        (object) $collection = new Collection(['one', 'two']);

        (object) $returnedValue = $collection->push('three');

        $this->assertSame($returnedValue, $collection);
        $this->assertCount(3, $collection->asArray());   
        $this->assertEquals(['one', 'two', 'three'], $collection->asArray());
    }

    public function test_adds_element_at_the_beggining()
    {
        (object) $collection = new Collection(['one', 'two']);

        (object) $returnedValue = $collection->pushAtTheBeginning('three');

        $this->assertSame($returnedValue, $collection);
        $this->assertCount(3, $collection->asArray());   
        $this->assertEquals(['three', 'one', 'two'], $collection->asArray());
    }

    public function test_returns_true_on_test()
    {
        (object) $collection = new Collection(['one', 'two', 'three']);

        $this->assertTrue($collection->test(function($element) {
            if ($element === 'three') {
                return true;
            }
        }));   
    }

    public function test_returns_false_on_test()
    {
        (object) $collection = new Collection(['one', 'two', 'three']);

        $this->assertFalse($collection->test(function($element) {
            if ($element === 'four') {
                return true;
            }
        }));   
    }

    public function test__returns_key_from_search()
    {
        (object) $collection = new Collection([
            'firstKey' => 'firstValue',
            'secondKey' => 'secondValue',
        ]);

        $this->assertIsString($collection->search('firstValue'));
        $this->assertEquals('firstKey', $collection->search('firstValue'));

        $this->assertIsString($collection->search('secondValue'));
        $this->assertEquals('secondKey', $collection->search('secondValue'));

        $this->assertIsBool($collection->search('thirdValue'));
        $this->assertFalse($collection->search('thirdValue'));
    }

    /**
     * @dataProvider lowestElementsProvider
     */
    public function testReturnsLowestElementFromAnArray(Array $allElements, Array $elementsToSearch, $expectedElement)
    {
        (object) $stepsCollection = new Collection($allElements);

        $this->assertEquals($expectedElement, $stepsCollection->getEarliest($elementsToSearch));   
    }

    /**
     * @dataProvider highestElementsProvider
     */
    public function testReturnsLatestElementFromAnArray(Array $allElements, Array $elementsToSearch, $expectedElement)
    {
        (object) $stepsCollection = new Collection($allElements);

        $this->assertEquals($expectedElement, $stepsCollection->getLatest($elementsToSearch));   
    }

    public function test_merges_array()
    {
        (object) $collection = new Collection([1, 2, 3, 4]);

        (object) $newCollection = $collection->concat([5, 6, 7]);
        (array) $newArray = [1, 2, 3, 4, 5, 6, 7];

        $this->assertInstanceOf(Collection::class, $newCollection);
        $this->assertEquals($newArray, $newCollection->asArray());
    }

    public function test_ungroups_collection_items_into_individual_arrays_of_keys_and_values() : void
    {
        (object) $collection = new Collection(a(
            name: 'my product',
            colors: ['red', 'blue', 'white'],
            sizes: new Collection(['s', 'l'])
        ));

        (object) $brokenItems = $collection->ungroup();

        $this->assertInstanceOf(expected: Collection::class, actual: $brokenItems);
        $this->assertEquals(expected: [
            new Collection(a(name: 'my product')),
            new collection(a(colors: 'red')),
            new collection(a(colors: 'blue')),
            new collection(a(colors: 'white')),
            new collection(a(sizes: 's')),
            new collection(a(sizes: 'l')),
        ], actual: ($brokenItems)->asArray());
    }

    public function test_groups_collection_items_into_individual_arrays_of_keys_and_values() : void
    {
        (object) $ungroupped = new Collection([
            new Collection(a(name: 'my product')),
            new collection(a(colors: 'red')),
            new collection(a(colors: 'blue')),
            new collection(a(colors: 'white')),
            a(sizes: 's'),
            a(sizes: 'l'),
        ]);

        (object) $groupped = new Collection(a(
            name: new Collection(['my product']),
            colors: new Collection(['red', 'blue', 'white']),
            sizes: new Collection(['s', 'l'])
        ));

        $this->assertEquals(expected: $groupped, actual: $ungroupped->group());
    }

    /**
    * @dataProvider filterProvider
    */
    public function test_filters_elements($callable, $exepectedResult)
    {
        (object) $collection = new Collection([
            'first' => 1, 
            'second' => 2, 
            'third' => 3, 
            'fourth' => 4, 
        ]);

        (object) $filteredCollection = $collection->filter($callable);


        $this->assertInstanceOf(Collection::class, $filteredCollection);
        $this->assertNotSame($collection, $filteredCollection);

        $this->assertSame($exepectedResult, $filteredCollection->asArray());
    }

    public function test_filters_using_variable_arguments_with_the_key_as_the_method_to_call_And_the_value_as_the_arguments_to_pass_it() : void
    {
        /** @var MockObject&CheckerFake */
        (object) $firstChecker = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $secondChecker = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $thidChecker = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $fourthChecker = $this->createMock(CheckerFake::class);

        (object) $items = new Collection([
            $firstChecker,
            $secondChecker,
            $thidChecker,
            $fourthChecker
        ]);

        (object) $expectedItems = [
            $firstChecker,
            $thidChecker
        ];

        // we expect as the argument:
        /*
            array(1) {
              [0]=>
              array(1) {
                ["isEnabled"]=>
                string(3) "yes"
              }
            }
         */

        $firstChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(true);
        $secondChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(false);
        $thidChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(true);
        $fourthChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(false);

        $this->assertSame(
            expected: $expectedItems, 
            actual: $items->getThoseThat(['isEnabled' => 'yes'])->getValues()->asArray()
        );

        //named parameters, you need php 8 plus to run this!
        $this->assertSame(
            expected: $expectedItems, 
            actual: $items->getThoseThat(isEnabled: 'yes')->getValues()->asArray()
        );
    }

    /**
    * @dataProvider flattenProvider
    */
    public function testMergesTheArraysOrCollectionIntoASingleCollection(Collection $collection, Collection $expectedCollection) : void
    {
        $this->assertEquals($expectedCollection, $collection->flatten());
    }


    public function test_test_reduces_array_keys_are_passed()
    {
        (object) $numbersLabels = new Collection([
            'one', 'two', 'three'
        ]);

        (object) $numbers = new Collection([
            1, 2, 3
        ]);

        $this->assertSame(
        /*
            IMPORTANT: WE'RE PASSING AN INTIAL VALUE OF 10!
        */16, $numbers->reduce(function(int $count, $number) : int {
            return $count + $number;
        }, $initial = 10));

        $this->assertSame('values:|0:one||1:two||2:three|', (string) $numbersLabels->reduce(function(string $label, $value, $key) : string {
            return "{$label}|{$key}:{$value}|";
        }, $initial = 'values:'));
    }

    /**
    * @dataProvider removeFilterProvider
    */
    public function test_filters_elements_and_removes_them_from_the_og_array($callable, array $exepectedResult, array $expectedOriginalArray)
    {
        (object) $collection = new Collection([
            'first' => 1, 
            'second' => 2, 
            'third' => 3, 
            'fourth' => 4, 
        ]);

        (object) $filteredCollection = $collection->filterAndRemove($callable);

        $this->assertInstanceOf(Collection::class, $filteredCollection);
        $this->assertNotSame($collection, $filteredCollection);
        $this->assertSame($exepectedResult, $filteredCollection->asArray());

        $this->assertSame($expectedOriginalArray, $collection->asArray());
    }

    /**
    * @dataProvider limitFilterProvider
    */
    public function test_filters_first_n_elements_callabck_must_not_be_called_after_limit_has_been_reached_for_performance_reasons($limit, $expectedNumberOftimesCallbackWasCalled,$callable, $exepectedResult)
    {
        (object) $collection = new Collection([
            'first' => 1, 
            'second' => 2, 
            'third' => 3, 
            'fourth' => 4, 
        ]);

        (object) $mockedCallback = $this->getMockBuilder(Invokable::class)
                                        ->onlyMethods(['__invoke'])
                                        ->getMock();

        $mockedCallback->expects($this->exactly($expectedNumberOftimesCallbackWasCalled))
                       ->method('__invoke')
                       ->will($this->returnCallback($callable));

        (object) $filteredCollection = $collection->filterFirst($limit, $mockedCallback);


        $this->assertInstanceOf(Collection::class, $filteredCollection);
        $this->assertNotSame($collection, $filteredCollection);

        $this->assertSame($exepectedResult, $filteredCollection->asArray());

        $this->assertSame($exepectedResult, $filteredCollection->asArray());
    }

    public function test_returna_new_array_skipping_the_first_number_of_items()
    {
        (object) $items = new Collection([
            'one', 'two', 'three', 'four', 'five', 'six'
        ]);

        $this->assertInstanceOf(Collection::class, $items->exceptfirst(2));
        $this->assertNotSame($items, $items->exceptfirst(2));
        $this->assertSame(['three', 'four', 'five', 'six'], $items->exceptFirst(2)->asArray());
    }

    /**
    * @dataProvider findFilterProvider
    */
    public function test_finds_first_element_callabck_must_not_be_called_after_limit_has_been_reached_for_performance_reasons($expectedNumberOftimesCallbackWasCalled,$callable, $exepectedResult)
    {
        (object) $collection = new Collection([
            'first' => 1, 
            'second' => 2, 
            'third' => 3, 
            'fourth' => 4, 
        ]);

        (object) $mockedCallback = $this->getMockBuilder(Invokable::class)
                                        ->onlyMethods(['__invoke'])
                                        ->getMock();

        $mockedCallback->expects($this->exactly($expectedNumberOftimesCallbackWasCalled))
                       ->method('__invoke')
                       ->will($this->returnCallback($callable));

        $this->assertSame($exepectedResult, $collection->find($mockedCallback));
    }

    public function test_finds_key_of_first_matching_element()
    {
        (object) $elements = new Collection([
            'one', 'two', 'three', 'four', 'five',
        ]);

        // first item
        $this->assertSame(0, $elements->findKey(function(string $value) : bool {
            return $value === 'one';                
        }));
        // second
        $this->assertSame(2, $elements->findKey(function(string $value) : bool {
            return $value === 'three';                
        }));

        // last item
        $this->assertSame(4, $elements->findKey(function(string $value) : bool {
            return $value === 'five';                
        }));
    }
    
    public function test_merges_array_preserves_keys()
    {
        (object) $collection = new Collection(['first' => 1, 'second' => 2]);

        (object) $newCollection = $collection->concat(['third' => 3]);
        (array) $newArray = ['first' => 1, 'second' => 2, 'third' => 3];

        $this->assertInstanceOf(Collection::class, $newCollection);
        $this->assertEquals($newArray, $newCollection->asArray());
    }

    public function test_sorts_array_returns_new_collection()
    {
        (object) $collection = new Collection([3, 1, 4, 2]);

        (object) $sortedCollection = $collection->sort(function($firstValue, $secondValue){
            if ($firstValue > $secondValue)  {
                return 1;
            } elseif ($firstValue < $secondValue) {
                return -1;
            } else {
                return 0;
            }
        });

        $this->assertInstanceof(Collection::class, $sortedCollection);
        $this->assertNotSame($collection, $sortedCollection);

        $this->assertEquals([1, 2, 3, 4], $sortedCollection->asArray());

    }

    public function test_sorts_by_key()
    {
        (object) $elements = new Collection([
            2 => 'two',
            1 => 'one',
            3 => 'three',
            0 => 'zero'
        ]);

        $this->assertSame([
            2 => 'two',
            1 => 'one',
            3 => 'three',
            0 => 'zero'
        ], $elements->asArray());

        $this->assertSame('two', $elements->first());

        // now let's actually sort by key
        (object) $newElements = $elements->sortByKey();

        $this->assertFalse(spl_object_hash($newElements) === spl_object_hash($elements));
        $this->assertSame([
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
        ], $newElements->asArray());

        $this->assertSame('zero', $newElements->first());

        // let's make sure the original is still not modified
        $this->assertSame([
            2 => 'two',
            1 => 'one',
            3 => 'three',
            0 => 'zero'
        ], $elements->asArray());
    }

    public function test_adds_and_gets_element_with_key()
    {
        (object) $collection = new Collection([]);

        (object) $returnedValue1 = $collection->add('isEnabled', true);
        (object) $returnedValue2 = $collection->add('fruit', 'watermelon');

        $this->assertEquals([
            'isEnabled' => true,
            'fruit' => 'watermelon'
        ], $collection->asArray());

        $this->assertSame($returnedValue1, $collection);
        $this->assertSame($returnedValue2, $collection);
        $this->assertSame(true, $collection->get('isEnabled'));
        $this->assertSame('watermelon', $collection->get('fruit'));
        $this->assertNull($collection->get('undefined key'));
    }

    public function test_gets___adds___checks___and_removes_from_string_manager()
    {
        (object) $collection = new Collection([
            'name' => 'rafael',
            'id' => 9
        ]);
        (object) $nameAsObject = new StringManager('name');

        $this->assertSame(true, $collection->hasKey($nameAsObject));
        $this->assertSame(false, $collection->hasKey(new StringManager('')));

        $this->assertSame('rafael', $collection->get($nameAsObject));

        $collection->remove($nameAsObject);

        $this->assertSame([
            'id' => 9
        ], $collection->asArray());

        $collection->add($nameAsObject, 'new name');

        $this->assertSame([
            'id' => 9,
            'name' => 'new name',
        ], $collection->asArray());
    }
    
    public function test_maps_items_and_stops_when_told_to()
    {
        (object) $numbers = new Collection([
            'one', 'two', 'three', 'four'
        ]);

        (object) $callableMock = $this->getMockBuilder(\stdClass::class)
                                      ->addMethods(['__invoke'])
                                      ->getMock();

        (integer) $timesCalled = 0;
        $callableMock->expects($this->exactly(3))
                     ->method('__invoke');


        (object) $result = $numbers->map(function($value, $key, callable $stop) use (&$timesCalled, $numbers, $callableMock) {
            $timesCalled++;
            $callableMock();

            (string) $number = $numbers->atPosition($timesCalled);

            if ($number === 'three') {
                $stop();
            }

            return "{$key}: {$number}";
        });

        $this->assertSame([
            '0: one', 
            '1: two', 
            '2: three',
        ], $result->asArray());
    }

    public function testMapsByCallingAMethodOnEachOneUsingNamedParameters() : void
    {
        /** @var MockObject&CheckerFake */
        (object) $firstChecker = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $secondChecker = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $thidChecker = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $fourthChecker = $this->createMock(CheckerFake::class);

        (object) $items = new Collection([
            $firstChecker,
            $secondChecker,
            $thidChecker,
            $fourthChecker
        ]);

        (object) $expectedItems = [
            true, true, false, false
        ];

        $firstChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(true);
        $secondChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(true);
        $thidChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(false);
        $fourthChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(false);

        $this->assertSame(
            expected: $expectedItems, 
            actual: $items->mapUsing(['isEnabled' => 'yes'])->getValues()->asArray()
        );

        //named parameters, you need php 8 plus to run this!
        $this->assertSame(
            expected: $expectedItems, 
            actual: $items->mapUsing(isEnabled: 'yes')->getValues()->asArray()
        );
    }

    /**
    * @dataProvider asJsonProvider
    */
    public function test_returns_json_representation($elements, $expectedJson)
    {
        (object) $collection = new Collection($elements);

        $this->assertEquals($expectedJson, $collection->asJson());
    }

    public function test_creates_collection_excluding_keys()
    {
        (array) $elements = [
            'id' => 76,
            'name' => 'Rafael',
            'password' => 'secret1234',
            'address' => 'undisclosed'
        ];

        (array) $expectedElementsAfterExclusion = [
            'name' => 'Rafael',
            'address' => 'undisclosed'
        ];

        (object) $collection = new Collection($elements);   

        (object) $filteredCollection = $collection->except(['id', 'password']);

        $this->assertInstanceof(Collection::class, $filteredCollection);
        $this->assertSame($expectedElementsAfterExclusion, $filteredCollection->asArray());
    }

    /**
    * @dataProvider includesProvider
    */
    public function test_creates_collection_including_keys_only($keysToInclude, $expectedElementsAfterInclusion)
    {
        (array) $elements = [
            'id' => 76,
            'name' => 'Rafael',
            'password' => 'secret1234',
            'address' => 'undisclosed'
        ];

        (object) $collection = new Collection($elements);   

        (object) $filteredCollection = $collection->only($keysToInclude);

        $this->assertInstanceof(Collection::class, $filteredCollection);
        $this->assertSame($expectedElementsAfterInclusion, $filteredCollection->asArray());
    }

    public function asJsonProvider()
    {
        return [
            'indexed' => [
                $elements = [1, 2, 4],
                $expectedJson = '[1,2,4]'
            ],
            'indexed' => [
                $elements = [
                    'type' => 'error',
                    'typeId' => 6
                ],
                $expectedJson = '{"type":"error","typeId":6}'
            ]
        ];   
    }

    /**
    * @dataProvider getsByFiledProvider
    */
    public function test_gets_by_field(Array $elements, $field, $value, $expectedElement)
    {
        (object) $collection = new Collection($elements);

        $this->assertEquals($expectedElement, $collection->getByField($field, $value));
    }

    /**
    * @dataProvider hasKeyProvider
    */
    public function test_check_has_key($elements, $key, $hasKey)
    {
        (object) $collection = new Collection($elements);

        $this->assertSame($hasKey, $collection->hasKey($key));
    }

    /**
    * @dataProvider removeElementProvider
    */
    public function test_removes_element_multably_if_exists($elements, $keyofElementToRemove, $expectedElements)
    {
        (object) $collection = new Collection($elements);

        (object) $returned = $collection->remove($keyofElementToRemove);

        $this->assertSame($collection, $returned);
        $this->assertSame($expectedElements, $collection->asArray());
    }

    /**
    * @dataProvider removeLastElementProvider
    */
    public function test_removes_LAST_element_multably_if_exists($elements, $expectedElements)
    {
        (object) $collection = new Collection($elements);

        (object) $returned = $collection->removeLast();

        $this->assertSame($collection, $returned);
        $this->assertSame($expectedElements, $collection->asArray());
    }

    /**
    * @dataProvider haveNoneProvider
    */
    public function test_have_none($elements, $haveNone)
    {
        (object) $collection = new Collection($elements);

        $this->assertSame($haveNone, $collection->haveNone());
    }

    /**
    * @dataProvider haveAnyOfProvider
    */
    public function test_have_any_of(array $collectionElements, array $elementsToCheck, bool $result)
    {
        (object) $collection = new Collection($collectionElements);

        $this->assertSame(expected: $result, actual: $collection->containAny($elementsToCheck));
    }

    /**
    * @dataProvider haveAllOfProvider
    */
    public function test_have_all_of(array $collectionElements, array $elementsToCheck, bool $result)
    {
        (object) $collection = new Collection($collectionElements);

        $this->assertSame(expected: $result, actual: $collection->containAll($elementsToCheck));
    }

    public function test_have_custom_callback()
    {
        (object) $items = new Collection([
            'one' => 1,
            'two' => 2,
            'three' => 3,
            'four' => 4
        ]);

        $this->assertTrue($items->have(function(int $item) : bool {
            return $item === 3;
        }));

        $this->assertFalse($items->have(function(int $item) : bool {
            return $item === 5;
        }));
    }

    public function test_have_custom_callback_NULL__BUG()
    {
        /**
         * Because using Collection::find() internally might return null 
         * when finding an item that is null, will resolve to false even though it was indeed found and it indeed has it.
         *
         * For example, if youre cchecking that all items in a collection are intanceof a type and you have null, the closure will return true on the null but false whn casting it to a boolean.
         */
        (object) $items = new Collection([
            'one' => 1,
            'two' => 2,
            'three' => null,
            'four' => 4
        ]);

        $this->assertTrue($items->have(function(mixed $item) : bool {
            return $item === null;
        }));
    }

    public function test_searches_for_values_string_manager()
    {
        (object) $collection = new Collection([new StringManager('rafa')]);

        $this->assertTrue($collection->contain('rafa'));   
    }
    
    /**
    * @dataProvider areProvider
    */
    public function test_compares_inner_array_to_given_array($elements, $elementsToCompareAgainst, $result)
    {
        (object) $collection = new Collection($elements);   

        $this->assertSame($result, $collection->are($elementsToCompareAgainst));
    }

    /**
    * @dataProvider NOTareProvider
    */
    public function test_compares_inner_array_to_given_array_NOT_equals($elements, $elementsToCompareAgainst, $result)
    {
        (object) $collection = new Collection($elements);   

        $this->assertSame($result, $collection->areNot($elementsToCompareAgainst));
    }

    public function test_gets_keys()
    {
        (object) $collection = new Collection([
            'first'  => 1,
            'second' => 2,
            'third'  => 3,
            4        => 4
        ]);

        $this->assertInstanceOf(Collection::class, $collection->getKeys());
        $this->assertEquals([new StringManager('first'), new StringManager('second'), new StringManager('third'), 4], $collection->getKeys()->asArray());
    }

    /**
    * @dataProvider notProvider
    */
    public function test_diff_elements_not_present_in_the_original_array($elements, $elementsToExclude, $expectedElements)
    {
        (object) $elements = new Collection($elements);

        $this->assertInstanceOf(Collection::class, $elements->notIn($elementsToExclude));
        $this->assertSame($expectedElements, $elements->notIn($elementsToExclude)->asArray());
    }

    public function test_reverses_array()
    {
        (object) $collection = new Collection([1 => 'one', 2 => 'two', 3 => 'three']);

        $this->assertInstanceof(Collection::class, $collection->reverse());
        $this->assertNotSame($collection, $collection->reverse());
        $this->assertSame(['one' => 1, 'two' => 2, 'three' => 3], $collection->reverse()->asArray());
    }

    public function test_inverses_array()
    {
        (object) $numbers = new Collection([1, 2, 3, 4, 5]);

        $this->assertInstanceof(Collection::class, $numbers->invert());   
        $this->assertNotSame($numbers, $numbers->invert());
        $this->assertSame([5, 4, 3, 2, 1], $numbers->invert()->asArray());
    }
    
    public function test_json_serializable()
    {
        (array) $elements = [
            'one' => 1,
            'two' => 2
        ];

        (object) $collection = new Collection($elements);

        $this->assertInstanceof(\JsonSerializable::class, $collection);
        $this->assertEquals($collection->jsonSerialize(), $elements);
        $this->assertEquals('{"one":1,"two":2}', json_encode($collection));
    }
    
    public function test_resets_indexed_keys()
    {
        (object) $collection = new Collection([
            '2' => 'two',
            '3' => 'three',
            '1' => 'one'
        ]);

        (array) $expectedElements = [
            '0' => 'two',
            '1' => 'three',
            '2' => 'one'
        ];

        (object) $result = $collection->resetKeys();

        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame($collection, $result);
        $this->assertSame($expectedElements, $collection->asArray());
    }

    public function test_its_iterable_as_an_array()
    {
        (array) $expectedNumbers = [
            'one' => 1,
            'two' => 2,
            'three' => 3,
        ];

        (object) $numbers = new Collection($expectedNumbers);

        (array) $generatedNumbers = [];
        foreach ($numbers as $key => $value) {
            $generatedNumbers[$key] = $value;
        }

        $this->assertSame($expectedNumbers, $generatedNumbers);
    }

    public function test_iterates_collection()
    {
        (object) $collection = new Collection([
            'one' => 1,
            'two' => 2,
            'three' => 3
        ]);   

        $result = $collection->forEvery(function(&$item){
            //NOTICE WE ARE NOT RETURNING THE VALUE
            if ($item === 1) {
                $item = 'first';
            } elseif ($item === 2) {
                $item = 'second';
            } elseif ($item === 3) {
                $item = 'third';
            }
        });

        (array) $expectedElements = [
            'one' => 'first',
            'two' => 'second',
            'three' => 'third'
        ];

        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame($collection, $result);
        $this->assertSame($expectedElements, $result->asArray());
        $this->assertSame($expectedElements, $collection->asArray());
    }

    public function test_iterates_collection_for_every_item_using_named_arguments_doesnt_exit_on_false_return() : void
    {
        /** @var MockObject&CheckerFake */
        (object) $firstChecker = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $secondChecker = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $thidChecker = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $fourthChecker = $this->createMock(CheckerFake::class);

        (object) $items = new Collection([
            $firstChecker,
            $secondChecker,
            $thidChecker,
            $fourthChecker
        ]);

        // we expect as the argument:
        /*
            array(1) {
              [0]=>
              array(1) {
                ["isEnabled"]=>
                string(3) "yes"
              }
            }
         */

        $firstChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(true);
        $secondChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(false);
        $thidChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(false);
        $fourthChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(false);

        $this->assertSame(
            expected: $items, 
            actual: $items->perform(['isEnabled' => 'yes'])
        );

        //named parameters, you need php 8 plus to run this!
        $this->assertSame(
            expected: $items, 
            actual: $items->perform(isEnabled: 'yes')
        );
    }

    public function testCallsTheGivenMethodOnEveryItemAndReturnsTrueIfAllOfThemReturnTrue() : void
    {
        /** @var MockObject&CheckerFake */
        (object) $firstChecker = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $secondChecker = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $thidChecker = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $fourthChecker = $this->createMock(CheckerFake::class);

        (object) $items = new Collection([
            $firstChecker,
            $secondChecker,
            $thidChecker,
            $fourthChecker
        ]);

        $firstChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(true);
        $secondChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(true);
        $thidChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(true);
        $fourthChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(true);

        $this->assertSame(
            expected: true, 
            actual: $items->allPass(['isEnabled' => 'yes'])
        );

        //named parameters, you need php 8 plus to run this!
        $this->assertSame(
            expected: true, 
            actual: $items->allPass(isEnabled: 'yes')
        );
    }

    public function testCallsTheGivenMethodOnEveryItemAndReturnsFalseIfOneOfThemReturnFalse() : void
    {
        /** @var MockObject&CheckerFake */
        (object) $firstChecker = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $secondChecker = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $thidCheckerFalse = $this->createMock(CheckerFake::class);
        /** @var MockObject&CheckerFake */
        (object) $fourthChecker = $this->createMock(CheckerFake::class);

        (object) $items = new Collection([
            $firstChecker,
            $secondChecker,
            $thidCheckerFalse,
            $fourthChecker
        ]);

        $firstChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(true);
        $secondChecker->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(true);
        $thidCheckerFalse->expects($this->exactly(2))->method('isEnabled')->with('yes')->willReturn(false);
        $fourthChecker->expects($this->exactly(0))->method('isEnabled')->with('yes')->willReturn(true);

        $this->assertSame(
            expected: false, 
            actual: $items->allPass(['isEnabled' => 'yes'])
        );

        //named parameters, you need php 8 plus to run this!
        $this->assertSame(
            expected: false, 
            actual: $items->allPass(isEnabled: 'yes')
        );
    }

    public function test_creates_colection_form_range()
    {
        (object) $collection = Collection::range(1, 4);

        $this->assertInstanceof(Collection::class, $collection);
        $this->assertSame([1, 2, 3, 4], $collection->asArray());
    }
    
    
    public function test_adds_an_array_of_elements()
    {
        (object) $collection = new Collection([
            'one' => 'first',
            'two' => 'second'
        ]);

        (object) $result = $collection->append([
            'three' => 'third',
            'four' => 'fourth'
        ]);

        (array) $expectedResult = [
            'one' => 'first', 
            'two' => 'second',
            'three' => 'third',
            'four' => 'fourth'
        ];

        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame($collection, $result);

        $this->assertEquals($expectedResult, $collection->asArray());
    }

    public function test_adds_an_indexed_array_of_elements()
    {
        (object) $collection = new Collection([
            0 => 'first',
            1 => 'second'
        ]);

        (object) $result = $collection->append([
            0 => 'third',
            1 => 'fourth'
        ]);

        (array) $expectedResult = [
            0 => 'first', 
            1 => 'second',
            2 => 'third',
            3 => 'fourth'
        ];

        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame($collection, $result);

        $this->assertEquals($expectedResult, $collection->asArray());
    }
    
    public function test_returns_list_string_manager()
    {
        (object) $collection = new Collection(['one', 'two', 'three']);

        $this->assertInstanceOf(StringManager::class, $collection->asList());
        $this->assertSame('one, two, three', $collection->asList()->get());

        //optional separatror
        $this->assertSame('one | two | three', $collection->asList(" |")->get());

        $this->assertSame('one and two and three', $collection->asList(" and")->get());
    }

    public function test_returns_a_new_collection_without_repeating_values()
    {
        (object) $numbers = new Collection([1, 2, 3, 4, 5, 3, 2, 4, 5, 6, 1]);

        (object) $uniqueNumbers = $numbers->withoutDuplicates();

        $this->assertNotSame($numbers, $uniqueNumbers);
        $this->assertSame([1, 2, 3, 4, 5, 6], $uniqueNumbers->asArray());
    }


    public function test_returns_array_without_last_element()
    {
        (array) $numbers = [1, 2, 3, 4, 5];
        (object) $numbersCollection = new Collection($numbers);

        $this->assertSame(5, $numbersCollection->count());

        (object) $newCollectionWithoutTheLastItem = $numbersCollection->withoutLast();

        $this->assertInstanceOf(Collection::class, $newCollectionWithoutTheLastItem);

        $this->assertNotSame($numbersCollection, $newCollectionWithoutTheLastItem);

        $this->assertSame(5, $numbersCollection->count());
        $this->assertSame(4, $newCollectionWithoutTheLastItem->count());

        $this->assertSame($numbers, $numbersCollection->asArray());
        $this->assertSame([1, 2, 3, 4], $newCollectionWithoutTheLastItem->asArray());
    }
    

    /**
    * @dataProvider allmatchProvider
    */
    public function test_all_match_a_given_tregular_expression($elements, $regularExpression, $expectedResult)
    {
        (object) $collection = new Collection($elements);

        $this->assertSame($expectedResult, $collection->allMatch($regularExpression));  
    }
    
    /**
    * @dataProvider areEqualProvider
    */
    public function test_array_or_collections_are_equal($firstValue, $secondValue, $areEqual)
    {
        $this->assertSame($areEqual, Collection::areEqual($firstValue, $secondValue));
    }

    public function test_creates_instance_from_comma_separated_string()
    {
        (object) $collection = Collection::createFromString('one, two, three');

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame([
            'one', 'two', 'three'
        ], $collection->asArray());
    }
    
    public function test_intersects_self_items()
    {
        (object) $collections = new Collection([
            new Collection(['one', 'two', 'three', 'four']),
            new Collection(['five', 'two', 'six', 'four']),
            ['seven', 'eight', 'four', 'ten', 'two']
        ]);

        $this->assertSame(['two', 'four'], $collections->intersect()->asArray());
    }

    public function test_intersects_current_items()
    {
        (object) $collections = new Collection([
            new Collection(['one', 'two', 'three', 'four']),
        ]);

        $this->assertSame(['one', 'two', 'three', 'four'], $collections->intersect()->asArray());
    }

    public function test_has_specific_item_loosely()
    {
        (object) $standardInstance = new \StdClass;

        (object) $items = new Collection([
            'one', null, $standardInstance, 2, [], new Class implements Comparable {
                public function isTheSameAs($value) : bool
                {
                    return $value === 'secretValue';
                }
            }
        ]);

        $this->assertTrue($items->have('one'));
        $this->assertFalse($items->have('two'));
        $this->assertTrue($items->have(''));
        $this->assertTrue($items->have($standardInstance));
        $this->assertTrue($items->have(new \StdClass));

        // comparable, strictness dont matter
        $this->assertTrue($items->have('secretValue'));
        $this->assertTrue($items->have($comparable = new Class Implements Comparable {
            public function isTheSameAs($value) : bool
            {
                // we have a 2 in the collection
                return $value === 2;   
            }
            
        }));
        $this->assertTrue($items->have($comparable));
    }

    public function test_has_specific_item_strict_comparison()
    {
        (object) $standardInstance = new \StdClass;

        (object) $items = new Collection([
            'one', null, $standardInstance, 2, [], new Class implements Comparable {
                public function isTheSameAs($value) : bool
                {
                    return $value === 'secretValue';
                }
            }
        ]);

        // strict 
        $this->assertTrue($items->strictlyHave('one'));
        $this->assertFalse($items->strictlyHave('two'));
        $this->assertFalse($items->strictlyHave(''));
        $this->assertTrue($items->strictlyHave($standardInstance));
        $this->assertFalse($items->strictlyHave(new \StdClass));

        // strict, null shount be converted to string
        $this->assertFalse($items->strictlyHave(''));
        $this->assertTrue($items->strictlyHave($standardInstance));
        // strict, different instance should not match
        $this->assertFalse($items->strictlyHave(new \StdClass));

        // comparable, doesnt aply here beacuse strictness DOES matter
        $this->assertFalse($items->strictlyHave('secretValue'));
        $this->assertFalse($items->strictlyHave($comparable = new Class Implements Comparable {
            public function isTheSameAs($value) : bool
            {
                // we strictlyHave a 2 in the collection
                return $value === 2;   
            }
            
        }));
    }

    /**
    * @dataProvider EqualityProvider
    */
    public function test_items_are_the_same_as(array $baseElements, array $elementsToCompare, bool $areEqual)
    {
        (object) $elements = new Collection($baseElements);

        $this->assertTrue(is_bool($elements->areTheSameAs(new Collection($elementsToCompare))));
        $this->assertSame($areEqual, $elements->areTheSameAs(new Collection($elementsToCompare)));
    }
    
    public function areEqualProvider()
    {
        return [
            'non array values ---> false' => [
                $firstValue = '',
                $secondValue = '',
                $areEqual = false
            ],
            'non array values integer ---> false' => [
                $firstValue = 1,
                $secondValue = '',
                $areEqual = false
            ],
            'non array values boolean ---> false' => [
                $firstValue = 1,
                $secondValue = true,
                $areEqual = false
            ],
            'same non array values integer ---> false' => [
                $firstValue = 1,
                $secondValue = 1,
                $areEqual = false
            ],
            'same non array values null ---> false' => [
                $firstValue = null,
                $secondValue = 1,
                $areEqual = false
            ],
            'one array, other non array ---> false' => [
                $firstValue = [],
                $secondValue = 0,
                $areEqual = false
            ],
            'one array, other non array (reversed) ---> false' => [
                $firstValue = 'true',
                $secondValue = ['true'],
                $areEqual = false
            ],
            'one array, other non array string ---> false' => [
                $firstValue = [],
                $secondValue = '',
                $areEqual = false
            ],
            'one array, other non array boolean ---> false' => [
                $firstValue = [],
                $secondValue = true,
                $areEqual = false
            ],
            'one array, other non-collection object ---> false' => [
                $firstValue = ['one' => 1],
                $secondValue = (object) ['one' => 1],
                $areEqual = false
            ],
            'both arrays, different values ---> false' => [
                $firstValue = [],
                $secondValue = ['true'],
                $areEqual = false
            ],
            'both arrays, same values (empty) ---> true' => [
                $firstValue = [],
                $secondValue = [],
                $areEqual = true
            ],
            'both arrays, same values (not empty) ---> true' => [
                $firstValue = ['one'],
                $secondValue = ['one'],
                $areEqual = true
            ],
        ];   
    }
    
    public function allmatchProvider()
    {
        return [
            'empty collection ---> false' => [
                $elements = [],
                $regularExpression = '/[A-Za-z0-9_\s]+/',
                $exepectedResult = false
            ],
            'one matches but not the other ---> false' => [
                $elements = ['rafa', 'this is not >Z>>matching|@##¢'],
                $regularExpression = '/^[A-Za-z0-9]+$/',
                $exepectedResult = false
            ],
            'non string elements ---> false' => [
                $elements = [[], (object) []],
                $regularExpression = '/^[A-Za-z0-9\s]+$/',
                $exepectedResult = false
            ],
            'all strings match but there is at least one non string element ---> false' => [
                $elements = ['rafa', 'serna', []],
                $regularExpression = '/^[A-Za-z0-9\s]+$/',
                $exepectedResult = false
            ],
            'all match ---> true' => [
                $elements = ['rafa', 'this will match'],
                $regularExpression = '/^[A-Za-z0-9\s]+$/',
                $exepectedResult = true
            ],
            'all match (single element) ---> true' => [
                $elements = ['rafa'],
                $regularExpression = '/^[A-Za-z0-9\s]+$/',
                $exepectedResult = true
            ],
        ];   
    }
    
    
    public function notProvider()
    {
        return [
            'numbers' => [
                $elements = [1, 2, 3, 4],
                $elementsToExclude = [2, 3, 3, 2],
                $expectedElements = [
                    0 => 1, 
                    3 => 4
                ]
            ],
            'numbers as collection' => [
                $elements = [1, 2, 3, 4],
                $elementsToExclude = new Collection([2, 3, 3, 2]),
                $expectedElements = [
                    0 => 1, 
                    3 => 4
                ]
            ],
            'numbers string manager' => [
                $elements = ['one', 'two', 'three'],
                $elementsToExclude = [new StringManager('two')],
                $expectedElements = [
                    0 => 'one', 
                    2 => 'three'
                ]
            ],
            'numbers string manager (reversed)' => [
                $elements = ['one', new StringManager('two'), 'three'],
                $elementsToExclude = ['two'],
                $expectedElements = [
                    0 => 'one', 
                    2 => 'three'
                ]
            ],
        ];   
    }
    
    public function areProvider()
    {
        return [
            'empty array ---> false' => [
                $elements = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4
                ],
                $elementsToCompareAgainst = [],
                $result = false
            ],
            'empty elements, to no empty array ---> false' => [
                $elements = [
                ],
                $elementsToCompareAgainst = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4
                ],
                $result = false
            ],
            'indexed array (values are equal but keys are not) ---> false' => [
                $elements = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4
                ],
                $elementsToCompareAgainst = [1, 3, 4],
                $result = false
            ],
            'indexed array (keys are equal but values are not) ---> false' => [
                $elements = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4
                ],
                $elementsToCompareAgainst = [
                    'one' => 0,
                    'two' => 0,
                    'three' => 0
                ],
                $result = false
            ],
            'incomplete array ---> false' => [
                $elements = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4
                ],
                $elementsToCompareAgainst = [
                    'one' => 1,
                    'two' => 3,
                ],
                $result = false
            ],
            'incomplete array (reversed) ---> false' => [
                $elements = [
                    'one' => 1,
                    'two' => 3,
                ],
                $elementsToCompareAgainst = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4
                ],
                $result = false
            ],
            'same keys and values ---> true' => [
                $elements = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4
                ],
                $elementsToCompareAgainst = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4,
                ],
                $result = true
            ],
            'given array stringManager, elements native string ---> true' => [
                $elements = [
                    'one' => 1,
                    'two' => 'second',
                ],
                $elementsToCompareAgainst = [
                    'one' => 1,
                    'two' => new StringManager('second'),
                ],
                $result = true
            ],
            'given array stringManager, elements stringManager (diferent instances with the same value) ---> true' => [
                $elements = [
                    'one' => 1,
                    'two' => new StringManager('second'),
                ],
                $elementsToCompareAgainst = [
                    'one' => 1,
                    'two' => new StringManager('second'),
                ],
                $result = true
            ],
            'given array native string, elements stringManager ---> true' => [
                $elements = [
                    'one' => 1,
                    'two' => new StringManager('second'),
                ],
                $elementsToCompareAgainst = [
                    'one' => 1,
                    'two' => 'second',
                ],
                $result = true
            ]
        ];   
    }

    public function NOTareProvider()
    {
        return [
            'empty array ---> true' => [
                $elements = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4
                ],
                $elementsToCompareAgainst = [],
                $result = true
            ],
            'empty elements, to no empty array ---> true' => [
                $elements = [
                ],
                $elementsToCompareAgainst = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4
                ],
                $result = true
            ],
            'indexed array (values are equal but keys are not) ---> true' => [
                $elements = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4
                ],
                $elementsToCompareAgainst = [1, 3, 4],
                $result = true
            ],
            'indexed array (keys are equal but values are not) ---> true' => [
                $elements = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4
                ],
                $elementsToCompareAgainst = [
                    'one' => 0,
                    'two' => 0,
                    'three' => 0
                ],
                $result = true
            ],
            'incomplete array ---> true' => [
                $elements = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4
                ],
                $elementsToCompareAgainst = [
                    'one' => 1,
                    'two' => 3,
                ],
                $result = true
            ],
            'incomplete array (reversed) ---> true' => [
                $elements = [
                    'one' => 1,
                    'two' => 3,
                ],
                $elementsToCompareAgainst = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4
                ],
                $result = true
            ],
            'same keys and values ---> false' => [
                $elements = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4
                ],
                $elementsToCompareAgainst = [
                    'one' => 1,
                    'two' => 3,
                    'three' => 4,
                ],
                $result = false
            ],
            'given array stringManager, elements native string ---> false' => [
                $elements = [
                    'one' => 1,
                    'two' => 'second',
                ],
                $elementsToCompareAgainst = [
                    'one' => 1,
                    'two' => new StringManager('second'),
                ],
                $result = false
            ],
            'given array stringManager, elements stringManager (diferent instances with the same value) ---> false' => [
                $elements = [
                    'one' => 1,
                    'two' => new StringManager('second'),
                ],
                $elementsToCompareAgainst = [
                    'one' => 1,
                    'two' => new StringManager('second'),
                ],
                $result = false
            ],
            'given array native string, elements stringManager ---> false' => [
                $elements = [
                    'one' => 1,
                    'two' => new StringManager('second'),
                ],
                $elementsToCompareAgainst = [
                    'one' => 1,
                    'two' => 'second',
                ],
                $result = false
            ]
        ];   
    }
    
    public function haveNoneProvider()
    {
        return [
            'no elements ---> true' => [
                $elements = [],
                $haveNone = true
           ],
           'one element ---> false' => [
                $elements = [1],
                $haveNone = false
           ],
           'more than one element ---> false' => [
                $elements = [1, 2, 3],
                $haveNone = false
           ],
           'one element associative ---> false' => [
                $elements = ['one' => 1],
                $haveNone = false
           ]
        ];   
    }

    public function haveAnyOfProvider()
    {
        return [
            'collection has no items, and neither does the elements to check -> false' => [
                $collectionElements = [/*'one', 'two', 'three'*/],
                $elementsToCheck = [],
                $result = false
            ],
            'collection has no items, but the elements to check do -> false' => [
                $collectionElements = [/*'one', 'two', 'three'*/],
                $elementsToCheck = ['one'],
                $result = false
            ],
            'collection has items, but the elements to check don"t -> false' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = [],
                $result = false
            ],
            'collection has items, and so do the elements to check (different array) -> false' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['four'],
                $result = false
            ],
            'collection has items, and so do the elements to check (same size, differnet values) -> false' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['four', 'five', 'six'],
                $result = false
            ],
            'collection has items, and so do the elements to check (same size, differnet values, but values matching keys) -> false' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = [1, 2, 3],
                $result = false
            ],
            'collection has items, and so do the elements to check (same size, differnet values, but values matching keys, reversed) -> false' => [
                $collectionElements = [1, 2, 3],
                $elementsToCheck = ['one', 'two', 'three'],
                $result = false
            ],
            'collection has items, and so do the elements to check (different array, reversed) -> false' => [
                $collectionElements = ['four'],
                $elementsToCheck = ['one', 'two', 'three'],
                $result = false
            ],
            'collection has items, and so do the elements to check (same array) -> true' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['one', 'two', 'three'],
                $result = true
            ],
            'collection has items, and so do the elements to check (elements to check is smaller, but all match) -> true' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['one', 'two'],
                $result = true
            ],
            'collection has items, and so do the elements to check (elements to check is smaller, but all match), differnet keys -> true' => [
                $collectionElements = [1 => 'one', 2 => 'two', 3 => 'three'],
                $elementsToCheck = ['uno' => 'one', 'dos' => 'two'],
                $result = true
            ],
            'collection has items, and so do the elements to check (elements to check is smaller and only one matches) -> true' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['one', 'four'],
                $result = true
            ],
            'collection has items, and so do the elements to check (elements to check is smaller and only one matches), different keys -> true' => [
                $collectionElements = [1 => 'one', 2 => 'two', 3 => 'three'],
                $elementsToCheck = ['uno' => 'one', 1 => 'four'],
                $result = true
            ],
            'collection has items, and so do the elements to check (elements to check is smaller and only one matches (the second one)) -> true' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['four', 'one'],
                $result = true
            ],
            'collection has items, and so do the elements to check (elements to check is larger, last doesn"t match) -> true' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['one', 'two', 'three', 'four'],
                $result = true
            ],
            'collection has items, and so do the elements to check (elements to check is larger, first doesn"t match) -> true' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['four', 'two', 'three', 'one'],
                $result = true
            ],
            //check with differnet keys, shoudl pass
        ];   
    }

    public function haveAllOfProvider()
    {
        return [
            'collection has no items, and neither does the elements to check -> true' => [
                $collectionElements = [/*'one', 'two', 'three'*/],
                $elementsToCheck = [],
                $result = true
            ],
            'collection has no items, but the elements to check do -> false' => [
                $collectionElements = [],
                $elementsToCheck = ['one'],
                $result = false
            ],
            'collection has items, but the elements to check don"t -> false' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = [],
                $result = false
            ],
            'collection has items, and so do the elements to check (different array) -> false' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['four'],
                $result = false
            ],
            'collection has items, and so do the elements to check (different array, reversed) -> false' => [
                $collectionElements = ['four'],
                $elementsToCheck = ['one', 'two', 'three'],
                $result = false
            ],
            'collection has items, and so do the elements to check (elements to check is smaller and only one matches) -> false' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['one', 'four'],
                $result = false
            ],
            'collection has items, and so do the elements to check (elements to check is smaller and only one matches (the second one)) -> false' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['four', 'one'],
                $result = false
            ],
            'collection has items, and so do the elements to check (elements to check is larger, last doesn"t match) -> false' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['one', 'two', 'three', 'four'],
                $result = false
            ],
            'collection has items, and so do the elements to check (elements to check is larger, first doesn"t match) -> false' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['four', 'two', 'three', 'one'],
                $result = false
            ],
            'collection has items, and so do the elements to check (same array) -> true' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['one', 'two', 'three'],
                $result = true
            ],
            'collection has items, and so do the elements to check (elements to check is smaller, but all match) -> true' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['one', 'two'],
                $result = true
            ],
            'collection has items, and so do the elements to check (elements to check is smaller (one item only), but all match) -> true' => [
                $collectionElements = ['one', 'two', 'three'],
                $elementsToCheck = ['one'],
                $result = true
            ],
            'collection has items, and so do the elements to check but with different keys (same values) -> true' => [
                $collectionElements = [1 => 'one', 2 => 'two', 3 =>'three'],
                $elementsToCheck = ['uno' => 'one', 'dos' => 'two', 'tres' => 'three'],
                $result = true
            ],
            //check with differnet keys, shoudl pass
        ];   
    }

    public function removeElementProvider()
    {
        return [
            'colletion with key' => [
                $elements = [
                    'name' => 'Rafael',
                    'age' => 'undisclosed'
                ],
                $keyofElementToRemove = 'name',
                $expectedElements = [
                    'age' => 'undisclosed'
                ],
           ],
           'colletion with nonexisting key' => [
                $elements = [
                    'name' => 'Rafael',
                    'age' => 'undisclosed'
                ],
                $keyofElementToRemove = 'adress',
                $expectedElements = [
                    'name' => 'Rafael',
                    'age' => 'undisclosed'
                ],
           ]
        ];   
    }

    public function removeLastElementProvider()
    {
        return [
            'colletion with key' => [
                $elements = [
                    'name' => 'Rafael',
                    'age' => 'undisclosed'
                ],
                $expectedElements = [
                    'name' => 'Rafael',
                ],
           ],
           'indexed' => [
                $elements = [
                    1, 2, 3, 4
                ],
                $expectedElements = [
                    1, 2, 3
                ],
           ]
        ];   
    }

    public function hasKeyProvider()
    {
        return [
            'has key --> true' => [
                $elements = [
                    'name' => 'Rafael',
                    'address' => 'secret'
                ],
                $key = 'name',
                $hasKey = true
           ],
           'does not have that key --> false' => [
                $elements = [
                    'name' => 'Rafael',
                    'address' => 'secret'
                ],
                $key = 'password',
                $hasKey = false
           ]
        ];   
    }
    public function getsByFiledProvider()
    {
        return [
            'exists ---> true' => [
                $elements = [
                    [
                        'name' => 'john',
                        'address' => 'undisclosed'
                    ],
                    [
                        'name' => 'rafa',
                        'address' => 'undisclosed2'
                    ]
                ],
                $field = 'name',
                $value = 'rafa',
                $expectedElement = [
                    'name' => 'rafa',
                    'address' => 'undisclosed2'
                ]
           ],
           'value doesnt exist ---> false' => [
                $elements = [
                    [
                        'name' => 'john',
                        'address' => 'undisclosed'
                    ],
                    [
                        'name' => 'rafa',
                        'address' => 'undisclosed2'
                    ]
                ],
                $field = 'name',
                $value = 'serna',
                $expectedElement = null
           ],
           'field doesnt exist ---> false' => [
                $elements = [
                    [
                        'name' => 'john',
                        'address' => 'undisclosed'
                    ],
                    [
                        'name' => 'rafa',
                        'address' => 'undisclosed2'
                    ]
                ],
                $field = 'country',
                $value = 'rafa',
                $expectedElement = null
           ],
           'array to serach is not multidimesional ---> false' => [
                $elements = [
                        'name' => 'rafa',
                        'address' => 'undisclosed2'
                ],
                $field = 'name',
                $value = 'rafa',
                $expectedElement = null
           ],
           'array to serach is empty ---> false' => [
                $elements = [
                ],
                $field = 'name',
                $value = 'rafa',
                $expectedElement = null
           ],
        ];   
    }

    public function lowestElementsProvider()
    {
        return [
            'lowest is "two"'  => [['one', 'two', 'three', 'four'], ['two', 'three', 'four'], 'two'],
            'lowest is "four"'  => [['four', 'five', 'six', 'seven'], ['six', 'seven', 'four'], 'four'],
        ];  
    }

    public function highestElementsProvider()
    {
        return [
            'highest is "four"'  => [['one', 'two', 'three', 'four'], ['two', 'three', 'four'], 'four'],
            'highest is "six"'  => [['four', 'five', 'six', 'seven'], ['six', 'five', 'four'], 'six'],
        ];   
    }

    public function atPopsitionProvider()
    {
        return [
            'first element' => [
                $position = 1,
                $expectedElement = 'one'
           ],
           'third element' => [
                $position = 3,
                $expectedElement = 'three'
           ],
           'last element' => [
                $position = 4,
                $expectedElement = 'four'
           ]
        ];   
    }

    public function containProvider()
    {
        return [
            'value to search is empty but array is not' => [
                $elements = [
                    1, 'two'
                ],
                $valueToSearch =  '',
                $result = false
           ],
           'value to search is empty but array is not (null)' => [
                $elements = [
                    1, 'two'
                ],
                $valueToSearch =  null,
                $result = false
           ],
            'value to search string manager, array native string' => [
                $elements = [
                    1, 'two'
                ],
                $valueToSearch =  new StringManager('two'),
                $result = true
           ],
           'value to search string manager, array stringManager' => [
                $elements = [
                    1, new StringManager('two')
                ],
                $valueToSearch =  new StringManager('two'),
                $result = true
           ],
           'value to search native string, array stringManager' => [
                $elements = [
                    1, new StringManager('two')
                ],
                $valueToSearch =  'two',
                $result = true
           ],
           'returns true if callable returns true' => [
                $elements = [
                    1, 2, 3, 4, 5
                ],
                $valueToSearch =  function($element) {
                    return $element === 3;
                },
                $result = true
           ],
           'returns false if callable returns false' => [
                $elements = [
                    1, 2, 3, 4, 5
                ],
                $valueToSearch =  function($element) {
                    return false;
                },
                $result = false
           ],
           'test searches existing integer ---> true' => [
                $elements = [
                    1, 2, 3, 4, 5
                ],
                $valueToSearch = 2,
                $result = true
           ],
           'test searches non existing integer ---> false' => [
                $elements = [
                    1, 2, 3, 4, 5
                ],
                $valueToSearch = 20,
                $result = false
           ],
           'test searches existing boolean ---> true' => [
                $elements = [
                    1, false
                ],
                $valueToSearch = false,
                $result = true
           ],
           'test searches non-existing boolean ---> false' => [
                $elements = [
                    1, false
                ],
                $valueToSearch = true,
                $result = false
           ],
           'test searches array ---> true' => [
                $elements = [
                    1, ['one']
                ],
                $valueToSearch = ['one'],
                $result = true
           ],
           'test searches non exisiting array ---> false' => [
                $elements = [
                    1, ['one']
                ],
                $valueToSearch = [],
                $result = false
           ],
           'value has string manager but array doesnt ---> true' => [
                $elements = [
                    'one', [new StringManager('two')]
                ],
                $valueToSearch = [
                    'two'
                ],
                $result = true
           ],
           'value has native string but array to search has string manager ---> true' => [
                $elements = [
                    'one', ['two']
                ],
                $valueToSearch = [
                    new StringManager('two')
                ],
                $result = true
           ],
           'value has string manager but array doesnt - Collection verison ---> true' => [
                $elements = [
                    'one', new Collection([new StringManager('two')])
                ],
                $valueToSearch = [
                    'two'
                ],
                $result = true
           ],
           //convert collections also, not only arrays
        ];   
    }
 
    public function filterProvider()
    {
       return [
           'all false' => [
                $callable = function(){
                    return false;
                },
                $exepectedResult = []
           ],
           'all true' => [
                $callable = function(){
                    return true;
                },
                $exepectedResult = [
                    'first' => 1, 
                    'second' => 2, 
                    'third' => 3, 
                    'fourth' => 4, 
                ]
           ],
           'some true, uses values' => [
                $callable = function($value) {
                    return $value === 1 || $value === 4;
                },
                $exepectedResult = [
                    'first' => 1, 
                    'fourth' => 4, 
                ]
           ],
           'some true, uses keys and values' => [
                $callable = function($value, $key) {
                    return $value === 2 || $key === 'third';
                },
                $exepectedResult = [
                    'second' => 2, 
                    'third' => 3, 
                ]
           ],
           'passes only value to non closures' => [
                $callable = 'is_string',
                $exepectedResult = [
                ]
           ]
       ];   
    }

    public function removeFilterProvider()
    {
       return [
           'all false' => [
                $callable = function(){
                    return false;
                },
                $exepectedResult = [],
                $expectedOriginalArray = [
                    'first' => 1, 
                    'second' => 2, 
                    'third' => 3, 
                    'fourth' => 4, 
                ]
           ],
           'all true' => [
                $callable = function(){
                    return true;
                },
                $exepectedResult = [
                    'first' => 1, 
                    'second' => 2, 
                    'third' => 3, 
                    'fourth' => 4, 
                ],
                $expectedOriginalArray = [] // all filtered and all removed
           ],
           'some true, uses values' => [
                $callable = function($value) {
                    return $value === 1 || $value === 4;
                },
                $exepectedResult = [
                    'first' => 1, 
                    'fourth' => 4, 
                ],
                $expectedOriginalArray = [
                    'second' => 2, 
                    'third' => 3, 
                ]
           ],
           'some true, uses keys and values' => [
                $callable = function($value, $key) {
                    return $value === 2 || $key === 'third';
                },
                $exepectedResult = [
                    'second' => 2, 
                    'third' => 3, 
                ],
                $expectedOriginalArray = [
                    'first' => 1, 
                    'fourth' => 4, 
                ]
           ],
           'passes only value to non closures' => [
                $callable = 'is_string',
                $exepectedResult = [
                ],
                $expectedOriginalArray = [
                    'first' => 1, 
                    'second' => 2, 
                    'third' => 3, 
                    'fourth' => 4, 
                ]
           ]
       ];   
    }

    public function limitFilterProvider()
    {
       return [
           'all false' => [
                $limit = 1,
                $expectedNumberOftimesCallbackWasCalled = 4, // called on every element
                $callable = function(){
                    return false;
                },
                $exepectedResult = []
           ],
           'all true, but result were limited to only the first two' => [
                $limit = 2,
                $expectedNumberOftimesCallbackWasCalled = 2,
                $callable = function(){
                    return true;
                },
                $exepectedResult = [
                    'first' => 1, 
                    'second' => 2, 
                ]
           ],
           'some true, uses values' => [
                $limit = 2,
                $expectedNumberOftimesCallbackWasCalled = 4, // called on every element since the last one was also on the limit
                $callable = function($value) {
                    return $value === 1 || $value === 4;
                },
                $exepectedResult = [
                    'first' => 1, 
                    'fourth' => 4, 
                ]
           ],
           'some true, uses keys and values' => [
                $limit = 2,
                $expectedNumberOftimesCallbackWasCalled = 3,
                $callable = function($value, $key) {
                    return $value === 2 || $key === 'third';
                },
                $exepectedResult = [
                    'second' => 2, 
                    'third' => 3, 
                ]
           ],
           'some true, uses keys and values, but limit is one' => [
                $limit = 1,
                $expectedNumberOftimesCallbackWasCalled = 2,
                $callable = function($value, $key) {
                    return $value === 2 || $key === 'third';
                },
                $exepectedResult = [
                    'second' => 2, 
                ]
           ],
       ];   
    }

    public function findFilterProvider()
    {
       return [
           'all false' => [
                $expectedNumberOftimesCallbackWasCalled = 4, // called on every element
                $callable = function(){
                    return false;
                },
                $exepectedResult = null // null when nothing has been found
           ],
           'all true, but result were limited to only the first two' => [
                $expectedNumberOftimesCallbackWasCalled = 1,
                $callable = function(){
                    return true;
                },
                $exepectedResult = 1
           ],
           'some true, uses values' => [
                $expectedNumberOftimesCallbackWasCalled = 1, // called on every element since the last one was also on the limit
                $callable = function($value) {
                    return $value === 1 || $value === 4;
                },
                $exepectedResult = 1
           ],
           'some true, uses keys and values' => [
                $expectedNumberOftimesCallbackWasCalled = 2,
                $callable = function($value, $key) {
                    return $value === 2 || $key === 'third';
                },
                $exepectedResult = 2
           ],
           'some true, uses keys and values (third)' => [
                $expectedNumberOftimesCallbackWasCalled = 3,
                $callable = function($value, $key) {
                    return $key === 'third';
                },
                $exepectedResult = 3
           ],
       ];   
    }
    
    public function lastKeyProvider()
    {
      return [
          'numbers' => [
              $elements = [1, 2, 3, 4],
              $expectedKey = 3
          ],
          'number' => [
              $elements = [1],
              $expectedKey = 0
          ],
          'associative' => [
              $elements = [
                'fruit' => 'watermelon',
                'vegetable' => 'onion'
              ],
              $expectedKey = 'vegetable'
          ],
          'empty' => [
              $elements = [
              ],
              $expectedKey = null
          ]
      ];   
    }

    public function containAllProvider()
    {
        return [
            'empty array' => [
                $elements = [],
                $elementsToCheck = [],
                $expectedResult = true
            ],
            'empty collection array' => [
                $elements = [],
                $elementsToCheck = ['one'],
                $expectedResult = false
            ],
            'empty array to check' => [
                $elements = ['one'],
                $elementsToCheck = [],
                $expectedResult = false
            ],
            'array to check contains one but not all' => [
                $elements = ['one', 'two'],
                $elementsToCheck = ['one', 'three'],
                $expectedResult = false
            ],
            'array to check contains all' => [
                $elements = ['one', 'two'],
                $elementsToCheck = ['one', 'two'],
                $expectedResult = true
            ],
            'array to check contains all, collection has other elements ---> true' => [
                $elements = ['one', 'three', 'two', 'four'],
                $elementsToCheck = ['one', 'two'],
                $expectedResult = true
            ],
            'array to check contains more than the collection ---> false' => [
                $elements = ['one'],
                $elementsToCheck = ['one', 'two'],
                $expectedResult = false
            ]
        ];   
    }
    
    public function callClosurecontainProvider()
    {
        return [
            'non closure callable' => [
                $valueToSearch = 'trigger_error',
                $expectedResult = true
            ],
            'closure callable' => [
                $valueToSearch = function(){return false;},
                $expectedResult = true
            ]
        ];   
    }
 
    public function includesProvider()
    {
        return [
            'array argument' => [
                $keysToInclude = ['id'],
                $expectedElementsAfterInclusion = [
                    'id' => 76
                ]
            ],
            'collection argument' => [
                $keysToInclude = new Collection(['id', 'name']),
                $expectedElementsAfterInclusion = [
                    'id' => 76,
                    'name' => 'Rafael'
                ]
            ]
        ];   
    }
 
    public function EqualityProvider()
     {
         return [
             'empty, true' => [
                 $baseElements = [],
                 $elementsToCompare = [],
                 $arethesame = true
             ],
             'first empty, second isnt, false' => [
                 $baseElements = [],
                 $elementsToCompare = ['one'],
                 $arethesame = false
             ],
             'first non empt, second empty, false' => [
                 $baseElements = ['one'],
                 $elementsToCompare = [],
                 $arethesame = false
             ],
             'both 1 but with a different value, false' => [
                 $baseElements = ['one'],
                 $elementsToCompare = ['two'],
                 $arethesame = false
             ],
             'both have the same values but one is larger then the other, false' => [
                 $baseElements = [2, 2],
                 $elementsToCompare = [2],
                 $arethesame = false
             ],
             '(reversed) both have the same values but one is larger then the other, false' => [
                 $baseElements = [2],
                 $elementsToCompare = [2, 2],
                 $arethesame = false
             ],
             'same values and lentgh, true' => [
                 $baseElements = [2],
                 $elementsToCompare = [2],
                 $arethesame = true
             ],
             'l2 same values and lentgh, true' => [
                 $baseElements = [2, 2],
                 $elementsToCompare = [2, 2],
                 $arethesame = true
             ],
             'same values but differnet index, should be true' => [
                 $baseElements = [
                    1 => 'one',
                    2 => 'two',
                    3 => 'three'
                ],
                 $elementsToCompare = [
                    3 => 'one',
                    1 => 'two',
                    6 => 'three'
                ],
                $arethesame = true
             ],
             'same as above but now one of them arrays has an extra elemeny, same values but differnet index, should be false' => [
                 $baseElements = [
                    1 => 'one',
                    2 => 'two',
                    3 => 'three',
                    4 => 'four'
                ],
                 $elementsToCompare = [
                    3 => 'one',
                    1 => 'two',
                    6 => 'three'
                ],
                $arethesame = false
             ],
             'different keys, some the same, should be false' => [
                 $baseElements = [
                    1 => 'none',
                    2 => 'two',
                    3 => 'three',
                ],
                 $elementsToCompare = [
                    3 => 'one',
                    1 => 'two',
                    6 => 'three'
                ],
                $arethesame = false
             ],
             'same keys, diff values, should be false' => [
                 $baseElements = [
                    1 => 'none',
                    2 => 'two',
                ],
                 $elementsToCompare = [
                    1 => 'three',
                    2 => 'four',
                ],
                $arethesame = false
             ]
         ];   
     }

     public function flattenProvider()
    {
      return [
          'only arrays' => [
              $collection = new Collection([
                    ['one', 'two'],
                    ['three', 'four', 'five'],
                    ['six'],
                    ['seven', 'eight']
              ]),
              $expectedCollection = new Collection([
                'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight'
              ])
          ],
          'arrays with a non array and non collection item' => [
              $collection = new Collection([
                    ['one', 'two'],
                    'three',
                    ['four', 'five'],
                    ['six'],
                    ['seven', 'eight']
              ]),
              $expectedCollection = new Collection([
                'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight'
              ])
          ],
          'only colletions' => [
              $collection = new Collection([
                    new Collection(['one', 'two']),
                    new Collection(['three', 'four', 'five']),
                    new Collection(['six']),
                    new Collection(['seven', 'eight']),
              ]),
              $expectedCollection = new Collection([
                'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight'
              ])
          ],
          'collections with a non array and non collection item' => [
              $collection = new Collection([
                    new Collection(['one', 'two']),
                    'three',
                    new Collection(['four', 'five']),
                    new Collection(['six']),
                    new Collection(['seven', 'eight']),
              ]),
              $expectedCollection = new Collection([
                'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight'
              ])
          ],
          'arrays, collections and non array and non collection item' => [
              $collection = new Collection([
                    new Collection(['one', 'two']),
                    'three',
                    new Collection(['four', 'five']),
                    new Collection(['six']),
                    ['seven', 'eight'],
              ]),
              $expectedCollection = new Collection([
                'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight'
              ])
          ],
      ];   
    }

    public function test_neblabs_collection() : void
    {
        $this->assertInstanceOf(Collection::class, neblabs_collection());
        $this->assertEquals(new Collection([]), neblabs_collection());
        $this->assertEquals(new Collection(['one', 'two', 'three']), neblabs_collection(['one', 'two', 'three']));
        $this->assertEquals(new Collection([['one', 'two'], ['three', 'four']]), neblabs_collection(
            [
                ['one', 'two'], 
                ['three', 'four']
            ]
        ));
        $this->assertEquals(new Collection(['one' => 'uno', 'two' => 'dos']), neblabs_collection(['one' => 'uno', 'two' => 'dos']));
    } 
}