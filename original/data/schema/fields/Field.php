<?php

namespace CouponURLs\Original\Data\Schema\Fields;

use CouponURLs\Original\Characters\StringManager;
use CouponURLs\Original\Data\Schema\Abilities\StructureField;

use function CouponURLs\Original\Utilities\Text\i;

class Field implements StructureField
{
    protected StringManager $name;
    protected StringManager $alias;

    public function __construct(
        string $name,
        string $alias = ''
    ) {
        $this->name = i($name);
        $this->alias = i($alias);
    }

    public function name(): StringManager
    {
        return $this->name;
    } 

    public function is(string $name): bool
    {
        return $this->name->__is($name) || $this->alias->__is($name);
    } 
}