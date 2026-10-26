<?php

namespace CouponURLs\Original\Collections;

use ArrayIterator;
use CouponURLs\Original\Abilities\Comparable;
use CouponURLs\Original\Abilities\Invokable;
use CouponURLs\Original\Characters\StringManager;
use CouponURLs\Original\Collections\Abilities\ArrayRepresentation;
use CouponURLs\Original\Collections\ArrayGetter;
use CouponURLs\Original\Collections\Mapper\Types;
use CouponURLs\Original\Collections\Stopper;
use BadMethodCallException;
use Closure;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

Class Collection implements IteratorAggregate, ArrayRepresentation, JsonSerializable
{
    protected $elements = [];
    protected $limitFilterElements = 0;
    protected $separator = ', ';

    public static function range($minimum, $maximum) : self
    {
        return new static(range($minimum, $maximum));   
    }

    public static function createFromString($stringRepresentation, $separator = null) : self
    {
        $separator = $separator?: ',';

        return new self(array_map(function($item) {
            return trim($item);
        }, explode($separator, $stringRepresentation)));
    }
    
    public static function create(/*Array|Collection*/ $elements) : self
    {
        return new static($elements ?? []);
    }

    public function __construct(/*Array|Collection*/ $elements = [])
    {
        $this->setElements(
            ArrayGetter::getArrayOrThrowExceptionFrom($elements ?? [])
        );
    }

    /**
     * If we set the elements somewhere down the line, 
     * we want to be able to change the implementation.
     * Right now it's an ArrayIterator, but if it changes in the future
     * we'd onlt have to o it in one place.
     */
    protected function setElements(array $elements)
    {
        $this->elements = new ArrayIterator($elements);
    }
    
    public function getIterator() : Traversable
    {
        return $this->elements;
    }
    
    public function asArray() : array
    {
        return $this->elements->getArrayCopy();
    }

    public function asJson() : StringManager
    {
        return new StringManager(wp_json_encode($this));   
    }

    public function asStringRepresentation() : StringManager
    {
        return $this->implode($this->separator);   
    }

    public function clean() : self
    {
        return new static(array_filter($this->asArray()));   
    }

    public function resetKeys() : self
    {
        $this->setElements(array_values($this->asArray()));

        return $this;   
    }
    

    public function push($element) : self
    {
        $this->elements[] = $element;

        return $this;
    }

    public function pushAtTheBeginning($element) : self
    {
        $this->setElements([
            $element,
            ...$this->elements->getArrayCopy()
        ]);

        return $this;
    }

    public function set($key, $value) : self
    {
        return $this->add($key, $value);   
    }

    public function setIfDoesNotHave(string $key, mixed $value) : self
    {
        if (!$this->hasKey($key)) {
            $this->set(key: $key, value: $value);
        }

        return $this;
    }

    public function add($key, $value) : self
    {
        $key = $key instanceof StringManager? (string) $key : $key;
        
        $this->elements[$key] = $value;   

        /**
         * One of the weirdness of php
         * is that internally an indexed array is not nesesarrily
         * ordered by its index by default
         * if we had a collection whose index starts at 3
         * and we added later on an item with an index of 2,
         * this item will be added at the end of the string
         * So let's fix that.
         */
        if ($this->isIndexed()) {
            return $this->sortByKey();
        }
        return $this;
    }

    public function append(/*Array|Colection*/ $elements, bool $keepNumericKeys = false) : self
    {   
        (array) $elements = ArrayGetter::getArrayOrThrowExceptionFrom($elements);

        foreach ($elements as $key => $value) {
            if (is_numeric($key) && !$keepNumericKeys) {
                $this->elements[] = $value;
            } else {
                $this->elements[$key] = $value;
            }
        }

        return $this;
    }

    public function prepend(/*Array|Colection*/ $elements, bool $keepNumericKeys = false) : self
    {   
        (array) $elements = ArrayGetter::getArrayOrThrowExceptionFrom($elements);

        foreach ($elements as $key => $value) {
            if (is_numeric($key) && !$keepNumericKeys) {
                $this->elements[] = $value;
            } else {
                $this->elements[$key] = $value;
            }
        }

        return $this;
    }
    
    public function appendAsArray(/*Array|Colection*/ $elements) : self
    {
        (array) $elements = ArrayGetter::getArrayOrThrowExceptionFrom($elements);

        foreach ($elements as $key => $value) {
            if (isset($this->asArray()[$key])) {
                if (is_array($this->asArray()[$key])) {
                    $this->elements[$key][] = $value;
                } else {
                    $this->elements[$key] = [$this->elements[$key], $value];
                }
            } else {
                $this->elements[$key] = [$value];
            }
        }

        return $this;
    }
    
    public function concat($arrayOrCollection) : self
    {
        if ($arrayOrCollection instanceof Collection) {
            $newElements = $arrayOrCollection->asArray();
        } else {
            $newElements = $arrayOrCollection;
        }

        return new static(array_merge($this->asArray(), $newElements));
    }

    public function merge($arrayOrCollection) : self
    {
        return $this->concat($arrayOrCollection);   
    }

    public function ungroup() : self
    {
        (object) $collection = new static([]);

        foreach ($this->elements as $key => $value) {
            if ((ArrayGetter::isArrayRepresentation($value))) {
                $brokenItems = (static::create(($value)))->map(
                    fn(mixed $item) => new static([$key => $item])
                );
                $collection->append($brokenItems);
            } else {
                $collection->push(new static([$key => $value]));
            }
        }

        return $collection;
    }

    public function group() : self
    {
        (object) $groups = new static([]);

        foreach ($this->elements as $value) {
            if ((ArrayGetter::isArrayRepresentation($value))) {
                (object) $item = new static($value);

                if (!$groups->hasKey($item->firstKey())) {
                    $groups->add(key: $item->firstKey(), value: new static([]));
                }

                $groups->get($item->firstKey())->push($item->first());
            }
        }

        return $groups;
    }

    public function mergeIf($condition, $arrayOrCollection) : self
    {
        if ($condition) {
            return $this->concat($arrayOrCollection); 
        }

        return $this;
    }
    
    public function first($numberOfItems = null)
    {
        if (!is_null($numberOfItems)) {
            (array) $elements = (array) $this->elements; // clone the current array
            return new Collection(array_slice($elements, 0, $numberOfItems));
        }

        (boolean) $isFirstIteration = true;

        foreach ($this->asArray() as $key => $value) {
            if ($isFirstIteration) {
                return $value;
            }
        }
    }

    public function exceptFirst(int $numberOfItems) : Collection
    {
        return new static(array_slice($this->asArray(), $numberOfItems));
    }
    

    public function last()
    {
        return $this->lastKey()? (isset($this->asArray()[$this->lastKey()])? $this->elements[$this->lastKey()] : null) : null;
    }

    public function haveMoreThan($number) : bool
    {
        return count($this->asArray()) > $number;
    }

    public function haveLessThan($number) : bool
    {
        return count($this->asArray()) < $number;
    }

    public function haveExactly($number) : bool
    {
        return count($this->asArray()) === $number;
    }

    public function haveAtLeast($number) : bool
    {
        return count($this->asArray()) >= $number;
    }

    public function haveMaximum($number) : bool
    {
        return count($this->asArray()) <= $number;
    }

    public function haveAny() : bool
    {
        return count($this->asArray()) > 0;
    }

    public function haveNone() : bool
    {
        return !$this->haveAny();
    }

    public function count() : int
    {
        return count($this->asArray());
    }

    public function valueIsNotNull(string $key) : bool
    {
        return $this->get($key) !== null;
    }
    
    public function isAssociative() : bool
    {
        foreach ($this->asArray() as $key => $value) {
            if (!is_string($key)) {
                return false;
            }
        }

        return true;
    }

    public function isIndexed() : bool
    {
        foreach ($this->asArray() as $key => $value) {
            if (!is_int($key)) {
                return false;
            }
        }

        return true;
    }
    

    public function atPosition($oneStartIndex)
    {
        (integer) $currentindex = 1;

        foreach ($this->asArray() as $element) {
            if ($currentindex === $oneStartIndex) {
                return $element;
            } else {
                $currentindex++;
            }
        }
        
    }

    public function hasKey($keyToSearch) : bool
    {
        $keyToSearch = (string) $keyToSearch;

        (array) $elements = $this->asArray();

        return isset($elements[$keyToSearch]);   
    }

    public function withoutDuplicates() : self
    {
        (object) $uniqueCollection = new static(array_unique($this->asArray()));

        return !$uniqueCollection->isAssociative()? $uniqueCollection->getValues() : $uniqueCollection;
    }
    
    public function search($value)
    {
        return array_search($value, $this->asArray());   
    }

    public function map(Callable $callable) : self
    {
        (object) $mappedCollection = new static([]);
        (object) $stop = new Stopper;

        foreach ($this->asArray() as $key => $value) {

            if ($callable instanceof \Closure) {
                $value = $callable($value, $key, $stop);
            } else {
                $value = call_user_func_array($callable, [$value]);
            }

            $mappedCollection->add($key, $value);

            if ($stop->shouldStop()) {
                break;
            }
        }

        return $mappedCollection;
        /*
        if ($useKeys) {
            return new static(array_map($callable, array_values($this->asArray()), array_keys($this->asArray())));
        }
        
        return new static(array_map($callable, $this->elements));*/
    }

    public function mapUsing(...$methodAsKeyAndValueAsTheMethodsValue) : Collection
    {
        [$methodName, $methodValue] = $this->getMethodNameAndValueFromVariableParameters(
            ...$methodAsKeyAndValueAsTheMethodsValue
        );
        //dr(debug_backtrace(limit: 10));

        return $this->map(
            fn(object|array $item) => is_array($item)? $item[$methodName]: $item->{$methodName}($methodValue)
        );
    }

    public function mapWithKeys(Callable $callable) : self
    {
        (array) $newArray = [];

        foreach($this->asArray() as $index => $element) {
            (array) $mappedData = $callable($element, $index);
            $newArray[$mappedData['key']] = $mappedData['value'];
        }

        return new static($newArray);
    }

    /**
     * MUTABLE ITERATION, RETURNS THE SAME INSTANCE
     */
    public function forEvery(Callable $callable, bool $breakOnFalse = false) : self
    {
        foreach ($this->elements as $key => &$value) {
            $result = $callable($value, $key);

            if ($breakOnFalse && $result === false) {
                break;
            }
        }

        return $this;
    }

    public function perform(...$methodAsKeyAndValueAsTheMethodsValue) : Collection
    {
        [$methodName, $methodValue] = $this->getMethodNameAndValueFromVariableParameters(
            ...$methodAsKeyAndValueAsTheMethodsValue
        );

        return $this->forEvery(
            function($item) use($methodName, $methodValue) : bool {

                $item->{$methodName}($methodValue);

                return true;
            }
        );
    }

    public function allPass(...$methodAsKeyAndValueAsTheMethodsValue) : bool
    {
        [$methodName, $methodValue] = $this->getMethodNameAndValueFromVariableParameters(
            ...$methodAsKeyAndValueAsTheMethodsValue
        );

        return $this->doesNotHave(fn($item) => !$item->{$methodName}($methodValue));
    }


    public function reduce(Callable $callback, $initial = '') : StringManager|string|int|float|self
    {
        /**
         * Can't use array_reduce since it dont support no indexes (keys)
         */
        /*mixed*/$reduceResult = $initial;
        (object) $stopper = new Stopper;

        foreach ($this->asArray() as $key => $value) {
            if ($stopper->shouldStop()) {
                break;
            }
            $reduceResult = $callback($reduceResult, $value, $key, $stopper);
        }

        //$reduceResult = array_reduce($this->asArray(), $callback, $initial);

        return Types::isString($reduceResult)? new StringManager((string) $reduceResult) : $reduceResult;
    }

    public function sum() : int|float
    {
        return array_sum($this->asArray());
    }

    public function reverse() : self
    {
        return new static(array_flip($this->asArray()));   
    }

    public function invert() : self
    {
        return new static(array_reverse($this->asArray()));   
    }
    
    public function asList($separator = ',') : StringManager
    {
        return $this->implode("{$separator} ")->trim("{$separator} ");
    }

    public function implode($separator = '') : StringManager
    {
        return new StringManager(implode($separator, $this->asArray()));   
    }

    public function filter(Callable $callable = null) : self
    {
        if (!is_callable($callable)) {
            return new static(array_filter($this->asArray()));
        }

        return $this->getFilteredElements($callable);
    }

    public function getThoseThat(...$methodAsKeyAndValueAsTheMethodsValue) : Collection
    {
        [$methodName, $methodValue] = $this->getMethodNameAndValueFromVariableParameters(
            ...$methodAsKeyAndValueAsTheMethodsValue
        );

        return $this->filter(
            fn(object $item) => $item->{$methodName}($methodValue)
        );
    }

    public function findTheOneThat(...$methodAsKeyAndValueAsTheMethodsValue) : mixed
    {
        [$methodName, $methodValue] = $this->getMethodNameAndValueFromVariableParameters(
            ...$methodAsKeyAndValueAsTheMethodsValue
        );

        return $this->find(
            fn(object $item) => $item->{$methodName}($methodValue)
        );
    }

    /**
     * Filters elements and removes the filtered elements from the original Collection instance.
     * 
     * @return Collection  A new Collection with the filtered items. The original collection gets the filtered elements     
     *                     permanently removed.
     */
    public function filterAndRemove(Callable $callable = null) : self
    {
        if (!is_callable($callable)) {
            $callable = array_filter(...);
        }

        return $this->getFilteredElements(function($element, $key) use ($callable) {
            (boolean) $shouldBeFiltered = $callable instanceof Closure || $callable instanceof Invokable? $callable($element, $key) : $callable($element);

            if ($shouldBeFiltered) {
                $this->remove($key);
                return true;
            }

            return false;
        });
    }

    public function filterFirst($limit, Callable $callable) : self
    {
        return $this->getFilteredElements($callable, $limit);
    }

    public function findHave(callable $callable) : self
    {
        return $this->getFilteredElements($callable, $limit = 1);
    }

    public function find(Callable $callable)
    {
        return $this->getFilteredElements($callable, $limit = 1)->first();   
    }

    public function flatten() : self     
    {
        (object) $flattenedCollection = new static([]);

        foreach ($this as $items) {
            if (is_array($items)) {
                $items = new static($items);
            } elseif (!($items instanceof Collection)) {
                $items = new static([$items]);
            }

            $flattenedCollection->append($items);
        }

        return ($flattenedCollection);
    }
    /**
     * Comparison is loose (== operator)
     * Comparable objects will be used, 
     * Collections and StringManagers supported 
     * for comparing against there native counterparts.
    */
    public function have($value) : bool
    {
        return $this->getCheckFunction($value)($value, $strictComparison = false)->haveAny();
    }

    public function doesNotHave($value) : bool
    {
        return !$this->have($value);
    }

    protected function getCheckFunction($value) : callable
    {
        if ($value instanceof \Closure) {
            return [$this, 'findHave'];
        }

        return [$this, 'checkHave'];
    }
    

    public function strictlyHave($value) : bool
    {
        return $this->checkHave($value, $strictComparison = true)->haveAny();   
    }

    /**
     * Diads are confusing to use, 
     * so we'll only use this one privately and
     * then we'll expose a more readable pulic API
     */
    protected function checkHave($value, bool $strictComparison = false) : self
    {
        return $this->filter(function($valueToCompareAgainst) use ($value, $strictComparison) : bool {
            if ($strictComparison) {
                return $value === $valueToCompareAgainst;
            }
            // lets first fallback to a Comparable obj
            if ($value instanceof Comparable) {
                return $value->isTheSameAs($valueToCompareAgainst);
            } else if ($valueToCompareAgainst instanceof Comparable) {
                return $valueToCompareAgainst->isTheSameAs($value);
            } 

            (array) $values = [
                [$value, $valueToCompareAgainst],
                [$valueToCompareAgainst, $value]
            ];

            if (is_object($value) || is_object($valueToCompareAgainst) && !(is_object($value) && is_object($valueToCompareAgainst))) {
                // only one of em is an object
                (string) $theObject = is_object($value) ? 'value' : 'valueToCompareAgainst';
                (string) $theValue = !is_object($value) ? 'value' : 'valueToCompareAgainst';

                if (is_array($$theValue)) {
                    $$theObject = $$theObject instanceof Collection? $$theObject->asArray() : [];
                }
                if (is_string($$theValue) || is_numeric($$theValue)) {
                    $$theObject = StringManager::isStringRepresentation($$theObject)? (string) $$theObject : '';
                }
            }

            return $value == $valueToCompareAgainst;
        });   
    }
    
    public function findKey(callable $finder) : int|string
    {
        return $this->map(function($value, $key, callable $stop) use ($finder) {
            if ($finder($value, $key)) {
                return $stop($key);
            }
        })->last();
    }

    protected function getFilteredElements(Callable $callable, $limit = 0) : self
    {
        (object) $filteredElements = new static([]);
        (integer) $numberOfFilteredElements = 0;

        foreach ($this->asArray() as $key => $value) {
            if ($callable instanceof Closure || $callable instanceof Invokable) {
                (boolean) $canBeIncluded = $callable($value, $key);
            } else {
                (boolean) $canBeIncluded = $callable($value);
            }

            if ($canBeIncluded) {
                $filteredElements->add($key, $value);
                $numberOfFilteredElements++;
            }

            if ($limit > 0 && $numberOfFilteredElements === $limit) {
                break;
            }
        }

        return $filteredElements;   
    }

    public function shift()
    {
        array_shift($this->asArray());   
    }

    /**
     * Array_pop without modifying the original array and without passing the popped value
     * @return Collection
     */
    public function withoutLast() : self
    {
        (array) $newArray = $this->asArray();

        array_pop($newArray);

        return new Collection($newArray);
    }
    
    /**
     * array_diff
     */
    public function notIn(/*ArrayRepresentation*/ $elements) : self
    {
        (array) $elements = ArrayGetter::getArrayOrThrowExceptionFrom($elements);

        return new static(array_diff($this->asArray(), $elements));
    }

    /**
     * Only checks for values regardless of ther key/index.
     * Comparison is loose.
     */
    public function areTheSameAs(Collection $elementsToCheck) : bool
    {
        if ($this->haveNone()) {
            return $elementsToCheck->haveNone();
        }

        if ($this->count() !== $elementsToCheck->count()) {
            return false;
        }

        return $this->reduce(function(bool $valueWasFound, $value, $key, Stopper $stop) use ($elementsToCheck) : bool {
            if (!$elementsToCheck->have($value)) {
                return $stop(false);
            }

            return $valueWasFound;
        }, $initial = $this->haveNone()? $elementsToCheck->haveNone() : true);
    }

    public function sort(Callable $callable = null) : self
    {
        (array) $elementsToSort = $this->asArray();

        if (is_null($callable)) {
            // a local varibale needs to be defined since it's passed by reference
            sort($elementsToSort);
        } else {
            usort($elementsToSort, $callable);
        }

        return new static($elementsToSort);   
    }

    public function sortByKey() : self
    {
        (array) $elements = $this->asArray();

        ksort($elements);

        return new static($elements);
    }

    public function except(/*Array|Collection*/ $keysToExclude) : self
    {
        (array) $keysToExclude = ArrayGetter::getArrayOrThrowExceptionFrom($keysToExclude);

        return (new static($this->asArray()))->filter(function($value, $key) use($keysToExclude) {
            return !in_array($key, $keysToExclude);
        });
    }

    public function only(/*Array|Collection*/ $keysToInclude) : self
    {
        (array) $keysToInclude = ArrayGetter::getArrayOrThrowExceptionFrom($keysToInclude);

        return new static(
            array_intersect_key($this->asArray(), array_flip($keysToInclude))
        );
    }

    /**
     * Compares the inner elements array (self::$elements) to the given array,
     * both arrays must be equal; StringManager values will be typecasted to regular
     * strings so different instances with the same value will evaluate to true
     */
    public function are(Array $itemsToCompare) : bool
    {
        if (count($itemsToCompare) !== $this->count()) {
            return false;
        }

        foreach ($itemsToCompare as $keyToCompare => $valueToCompare) {
            if (!$this->hasKey($keyToCompare)) {
                return false;
            }
            $selfValue = $this->get($keyToCompare);

            if ($valueToCompare instanceof StringManager) {
                $valueToCompare = $valueToCompare->get();
            }

            if ($selfValue instanceof StringManager) {
                $selfValue = $selfValue->get();
            }

            if ($selfValue !== $valueToCompare) {
                return false;
            }
        }

        return true;
    }
    public function areNot(Array $items) : bool
    {
        return !$this->are($items);
    }
    
    public function contain($itemToSearch) : bool
    {
        if ($itemToSearch instanceof Closure) {
            return $this->filter($itemToSearch)->haveAny();
        }

        if (Types::isString($itemToSearch)) {
            return in_array(strtolower($itemToSearch), $this->map(StringManager::convertToLowerCase())->asArray());
        }

        if (is_array($itemToSearch) || $itemToSearch instanceof Collection) {
            $itemToSearch = ArrayGetter::getArrayOrThrowExceptionFrom($itemToSearch);
            $itemToSearch = (new static($itemToSearch))
                            ->map(static::convertToString())
                            ->asArray();
        }

        return in_array(
            $itemToSearch, 
            $this->map(static::convertToString())
                 ->map(function($value){return ($value instanceof Collection)? $value->asArray() : $value;})
                 ->asArray(), 
            $strictTypeSearch = true
        );
    }

    //checks that the given collection exists within this collection
    //INCLUDING THE KEYS
    public function containCollection(array|Collection $collection) : bool
    {
        (array) $arrayToCheck = ArrayGetter::getArrayOrThrowExceptionFrom($collection);

        foreach ($arrayToCheck as $key => $value) {
            if ($this->get($key) != $value) {
                return false;
            }
        }

        return true;
    }

    public function containAny(array|Collection $elements) : bool
    {
        (array) $elements = ArrayGetter::getArrayOrThrowExceptionFrom($elements);

        foreach($elements as $element) {
            if ($this->contain($element)) {
                return true;
            }
        }

        return false;
    }

    public function containAll(/*Colection|Array*/$elements) : bool
    {

        if ($elements instanceof Collection) {
            $elements = $elements->asArray();
        }

        if (empty($elements) && $this->haveAny()) return false;

        foreach($elements as $element) {
            if (!$this->contain($element)) {
                return false;
            }
        }

        return true;  
    }
    
    public function allMatch($regularExpression) : bool
    {
        if ($this->haveNone()) return false;

        foreach ($this->asArray() as $element) {
            if (is_string($element)) {
                $element = new StringManager($element);
            } elseif (!($element instanceof StringManager)) {
                return false;
            }
            if (!$element->matchesRegEx($regularExpression)) {
                return false;
            }
        }

        return true;
    }
    
    public function intersect(...$collections) : Collection
    {
        if (empty($collections)) {
            // we'll intersect the current elements
            $collections = $this->asArray();
        }


        $collections = array_map(function($item) {
            return ($item instanceof Collection)? $item->asArray() : $item;
        }, $collections);

        if (count($collections) < 2) {
            return new Collection((new Collection($collections))->first());
        }

        return new Collection(array_values(array_intersect(...$collections)));
    }
    
    public function test(Callable $callable) : bool
    {
        foreach ($this->asArray() as $element) {
            (boolean) $hasItpassed = ($callable($element) === true);

            if ($hasItpassed) {
                return true;
            }
        }

        return false;
    }

    public function get($key, mixed $default = null)
    {
        $key = (string) $key;

        if ($this->hasKey($key)) {
            return $this->elements[$key];
        }   

        return $default;
    }

    public function __get($name)
    {
        return $this->get($name);
    } 

    public function remove($key) : self
    {
        $key = (string) $key;

        if ($this->hasKey($key)) {
            unset($this->elements[$key]);
        }

        return $this;
    }

    public function removeFirst() : self
    {
        $this->remove($this->firstKey());

        return $this;   
    }

    public function removelast() : self
    {
        $this->remove($this->lastKey());

        return $this;   
    }

    public function firstKey()
    {
        foreach ($this->asArray() as $key => $value) {
            return $key;
        }   
    }

    public function lastKey()
    {
        if (function_exists('array_key_last')) { // better performance on big arrays
            return array_key_last($this->asArray());
        }

        (string) $lastestKey = null;

        foreach ($this->asArray() as $key => $value) {
            $lastestKey = $key;            
        }   

        return $lastestKey;
    }

    public function getKeys() : self
    {
        return (new static(array_keys($this->asArray())))->map($this->valueToStringManager());   
    }
    
    public function getValues() : self
    {
        return new static(array_values($this->asArray()));   
    }
    

    public function getEarliest(Array $elementsToSearch)
    {
        return $this->getValueSortedBy(function($index, $validElement, $currentPosition) {
            return ($index < $currentPosition)? $index : false;
        }, $elementsToSearch);

    }

    public function getLatest(Array $elementsToSearch)
    {
        return $this->getValueSortedBy(function($index, $validElement, $currentPosition) {
            return ($index > $currentPosition)? $index : false;
        }, $elementsToSearch, $currentPosition = 0);
    }

    public function getByField($field, $value)
    {
        $key = array_search($value, array_column($this->asArray(), $field));

        if (($key !== false) && isset($this->asArray()[$key])) {
            return $this->elements[$key];
        }
    }

    public function setSeparator($separator)
    {
        if ($separator) {
            $this->separator = $separator;
        }
    }

    public static function areEqual($collectionOrArray1, $collectionOrArray2)
    {
        if (!($collectionOrArray1 instanceof Collection) && (!is_array($collectionOrArray1))) {
            return false;
        } elseif (!($collectionOrArray2 instanceof Collection) && (!is_array($collectionOrArray2))) {
            return false;
        }

        (array) $array1 = ArrayGetter::getArrayOrThrowExceptionFrom($collectionOrArray1);
        (array) $array2 = ArrayGetter::getArrayOrThrowExceptionFrom($collectionOrArray2);

        return $array1 === $array2;
    }
    
    public function jsonSerialize() : array
    {
        return $this->asArray();   
    }
    
    protected function getValueSortedBy(Callable $sortType, Array $elementsToSearch, $currentPosition = 1000000)
    {
        (array) $validElements = array_intersect($this->asArray(), $elementsToSearch);

        foreach ($validElements as $index => $validElement) {
            $result = $sortType($index, $validElement, $currentPosition);

            if (is_int($result)) {
                $currentPosition = $result;
            }
        }

        return isset($this->asArray()[$currentPosition])? $this->elements[$currentPosition] : null;
    }

    protected function valueToStringManager()
    {
        return function($key){
            if (is_string($key)) {
                return new StringManager($key);
            }
            
            return $key;
        };   
    }

    public static function convertToString()
    {
        return function($value) {
            return StringManager::stringToNative($value);           
        };            
    }

    protected function getMethodNameAndValueFromVariableParameters(...$methodAsKeyAndValueAsTheMethodsValue) : array
    {
        (boolean) $passedANamedParameter = is_string(
            array_key_first($methodAsKeyAndValueAsTheMethodsValue)
        );

        if ($passedANamedParameter) {
            $methodAsKeyAndValueAsTheMethodsValue[0] = $methodAsKeyAndValueAsTheMethodsValue;
        }

        (string) $methodName = array_key_first($methodAsKeyAndValueAsTheMethodsValue[0]);
        /*mixed*/ $methodValue = $methodAsKeyAndValueAsTheMethodsValue[0][$methodName];

        return [$methodName, $methodValue];
    }
}