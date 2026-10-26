<?php

namespace CouponURLs\Original\Data\Schema;

use CouponURLs\Original\Characters\StringManager;
use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Utilities\TypeChecker;

Class DatabaseColumnChanges extends DatabaseColumn
{
    use TypeChecker;

    public function __construct($name, $type = 'VARCHAR(250)', $extra = '')
    {
        $this->fromColumn = $this->expect($name['from'])->toBe(DatabaseColumn::class);
        $this->toColumn = $this->expect($name['to'])->toBe(DatabaseColumn::class);
    }

    public function hasNameChange()
    {
        return $this->fromColumn->getName() === $this->toColumn->getName();   
    }

    /**
     * Currently, only changing the name or type is supported
     *
     * PLEASE NOTE, CHANGING THE COLUMN NAME AND THE LATER ON ADDING A NEW COLUMN WITH THE
     * OLD NAME IS **NOT** SUPPORTED AND MAY INTRODUCE SOME SERIOUS BUGS.
     * 
     */
    public function columnNeedsToBeUpdated(array $fieldsFromProductionDatabase)
    {
        /*array|null*/ $fieldToUpdate =  Collection::create($fieldsFromProductionDatabase)->find(function($field) {
            (string) $name = StringManager::create((string) $field['Field']?: $field['field'])->trim();

            if ($this->hasNameChange()) {
                return $name->__is($this->fromColumn->getName());
            }

            return $name->__is($this->toColumn->getName());
        });

        if (is_array($fieldToUpdate)) {
            // here we'll check if the field definiton has changed
            (string) $type = StringManager::create($fieldToUpdate['Type']?: $fieldToUpdate['type'])->trim()->toLowerCase();

            return !$type->__is($this->toColumn->getClean('type')->trim()->toLowerCase());
        }

        return false;
    }

    public function getOldColumn()
    {
        return $this->fromColumn;   
    }
 
    public function getNewColumn()
    {
        return $this->toColumn;   
    }   
}