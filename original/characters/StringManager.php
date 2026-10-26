<?php

namespace CouponURLs\Original\Characters;

use DateTime;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\ArrayGetter;
use CouponsPlus\Original\Collections\Mapper\Types;
use JsonSerializable;
use Stringy\Stringy;

Class StringManager extends Stringy implements JsonSerializable
{
    public static function isStringRepresentation($value)
    {
        return is_string($value) || ($value instanceof static) || (is_object($value) && method_exists($value, '__toString'));
    }

    public function isEmpty(): bool
    {
        return $this->trim()->length() === 0;
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    public function hasValue()
    {
        return $this->isNotEmpty();
    }

    public function ___is($text, $caseInsensitive = false)
    {
        if (is_array($text)) {
            return false;
        }

        if ($caseInsensitive) {
            return strtolower($this->get()) === strtolower((string) $text);
        }

        return $this->get() === ((string) $text);
    }

    public function isNot($text)
    {
        return !$this->___is($text);
    }

    public function isEither(/*ArrayRepresentation*/ $strings)
    {
        $strings = ArrayGetter::getArrayOrThrowExceptionFrom($strings);

        (object) $strings = new Collection($strings);
        return $strings->contain((string) $this->toLowerCase()->get());
    }

    public function isNotEither(/*ArrayRepresentation*/ $strings)
    {
        return !$this->isEither($strings);
    }

    public function get()
    {
        return (string) $this;
    }

    public function explode(string $delimiter, ?int $limit = null) : array
    {
        return (new Collection(explode($delimiter, $this->get())))
            ->map(function($piece){return new StringManager($piece);})
            ->filter(
                function(StringManager $piece){
                    return $piece->trim()->hasValue();
                }
            )->asArray();
    }

    public function getAlphanumeric()
    {
        return $this->getOnly('A-Za-z0-9_\s');
    }

    public function matches($pattern)
    {
        $str = $this->get();

        $r = preg_match_all($pattern, $this->str);

        return $r == 1;
    }

    public function replaceRegEx($pattern, /*string|array|callable*/ $replacement)
    {
        if (is_callable($replacement)) {
            $result = preg_replace_callback($pattern, $replacement, $this->get());
        } else {
            $result = preg_replace($pattern, $replacement, $this->get());
        }

        return new static($result);
    }

    public function getOnly($pattern)
    {
        return $this->replaceRegEx("/[^{$pattern}]/", '');
    }

    public function decodeUrl()
    {
        return new static(urldecode($this->get()));
    }

    public function isDate($format)
    {
        (object) $date = DateTime::createFromFormat($format, $this->get());

        return ($date instanceof DateTime) && ($date->format($format) === $this->get());
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->str;
    }

    public static function convertToLowerCase()
    {
        return function($value) {
            if (Types::isString($value)) {
                return strtolower((string) $value);
            }

            return $value;
        };
    }

    public static function stringToNative($value)
    {
        if ($value instanceof StringManager) {
            return (string) $value;
        } elseif (is_array($value) || $value instanceof Collection) {
            (object) $valueCollection = is_array($value)? new Collection($value) : $value;

            (object) $convertedCollection = $valueCollection->map(function($value) {
                return static::stringToNative($value);
            });

            return is_array($value)? $convertedCollection->asArray() : $convertedCollection;
        }

        return $value;
    }
}
