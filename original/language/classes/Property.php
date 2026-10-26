<?php

namespace CouponURLs\Original\Language\Classes;

use CouponURLs\Original\Characters\StringManager;
use CouponURLs\Original\Collections\Collection;
use function CouponURLs\Original\Utilities\Collection\_a;

Class Property
{
    private $name;
    private $type;

    public function __construct(string $name, string $type = '', string $visibility = '')
    {
        $this->name = $name;
        $this->type = $type;   
        $this->visibility = $visibility;
    }

    public function getShortType() : string
    {
        if ($this->type === 'collection') {
            return 'Collection';
        }

        return _a(StringManager::create($this->type)->explode('\\'))->last() ?? '';
    }

    public function getLongType() : string
    {
        if ($this->type === 'collection') {
            return Collection::class;
        }

        return $this->type;
    }

    public function isTyped() : bool
    {
        return (boolean) $this->type;   
    }

    public function getName() : string
    {
        return $this->name;   
    }

    public function getVisibility() : string
    {
        return $this->visibility;   
    }
}