<?php

namespace CouponURLs\tests\unit\original\data\schema;

use CouponURLs\Original\Data\Schema\DatabaseColumn;
use CouponURLs\Original\Data\Schema\DatabaseColumnChanges;

Class DatabaseColumnChangesTest extends \WP_UnitTestCase
{
    /**
    * @dataProvider columnNeedsToBeUpdatedProvider
    */
    public function test_checks_if_it_needs_to_be_updated(DatabaseColumn $from, DatabaseColumn $to, $fields, $columnNeedsToBeUpdated)
    {
        (object) $databaseColumnChanges = new DatabaseColumnChanges([
            'from' => $from,
            'to'   => $to
        ]);

        $this->assertSame($columnNeedsToBeUpdated, $databaseColumnChanges->columnNeedsToBeUpdated($fields));
    }

    public function columnNeedsToBeUpdatedProvider()
    {
        return [
            "several fields, none of which include a 'from' or 'to' column ---> false" => [
                $from =  new DatabaseColumn('value',         'VARCHAR(250)'),
                $to =    new DatabaseColumn('value',         'MEDIUMTEXT'),
                $fields = [
                    [
                        "Field" =>  "base_path",
                        0 =>  "base_path",
                        "Type" =>  "varchar(100)",
                        1 =>  "varchar(100)",
                        "Null" =>  "YES",
                        2 =>  "YES",
                        "Key" =>  "UNI",
                        3 =>  "UNI",
                    ],
                    [
                        "Field" =>  "id",
                        0 =>  "id",
                        "Type" =>  "MEDIUMTEXT",
                        1 =>  "MEDIUMTEXT",
                        "Null" =>  "YES",
                        2 =>  "YES",
                        "Key" =>  "UNI",
                        3 =>  "UNI",
                    ],
                ],
                $columnNeedsToBeUpdated = false
            ],
            "several fields, has no name change and one of them exists and has a different type than the type of the old column but has the same type as the new column ---> false" => [
                $from =  new DatabaseColumn('value',         'VARCHAR(250)'),
                $to =    new DatabaseColumn('value',         'MEDIUMTEXT'),
                $fields = [
                    [
                        "Field" =>  "base_path",
                        0 =>  "base_path",
                        "Type" =>  "varchar(100)",
                        1 =>  "varchar(100)",
                        "Null" =>  "YES",
                        2 =>  "YES",
                        "Key" =>  "UNI",
                        3 =>  "UNI",
                    ],
                    [
                        "Field" =>  "value",
                        0 =>  "value",
                        "Type" =>  "MEDIUMTEXT",
                        1 =>  "MEDIUMTEXT",
                        "Null" =>  "YES",
                        2 =>  "YES",
                        "Key" =>  "UNI",
                        3 =>  "UNI",
                    ],
                ],
                $columnNeedsToBeUpdated = false
            ],
            "several fields, has no name change and one of them exists and has a different type than the type of the NEW column ---> true" => [
                $from =  new DatabaseColumn('value',         'VARCHAR(250)'),
                $to =    new DatabaseColumn('value',         'MEDIUMTEXT'),
                $fields = [
                    [
                        "Field" =>  "base_path",
                        0 =>  "base_path",
                        "Type" =>  "varchar(100)",
                        1 =>  "varchar(100)",
                        "Null" =>  "YES",
                        2 =>  "YES",
                        "Key" =>  "UNI",
                        3 =>  "UNI",
                    ],
                    [
                        "Field" =>  "value",
                        0 =>  "value",
                        "Type" =>  "VARCHAR(250)",
                        1 =>  "VARCHAR(250)",
                        "Null" =>  "YES",
                        2 =>  "YES",
                        "Key" =>  "UNI",
                        3 =>  "UNI",
                    ],
                ],
                $columnNeedsToBeUpdated = true
            ]
        ];   
    }
    
}
