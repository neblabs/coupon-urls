<?php

namespace CouponURLs\tests\unit\original\data;

use CouponURLs\Original\Data\Drivers\DatabaseDriver;
use CouponURLs\Original\Data\Model\Domain;
use CouponURLs\Original\Data\Model\Gateway;
use CouponURLs\Original\Data\Schema\DatabaseColumn;
use CouponURLs\Original\Data\Schema\DatabaseTable;
use CouponURLs\tests\unit\original\data\doubles\DomainWithPreparationMethod;

Class _GatewayTes_t extends \WP_UnitTestCase
{
    public function test_calls_before_insertion_only_if_it_exists()
    {
        (object) $gateway = $this->getMockBuilder(Gateway::class)
                                 ->disableOriginalConstructor()
                                 ->onlyMethods(['model'])
                                 ->getMock();

        $gateway->expects($this->exactly(2))
                ->method('model')
                ->willReturn([ 
                    'table' => $this->createMock(DatabaseTable::class),
                    'domain' => 'domain'
                ]);

        (object) $databaseDriverMock = $this->getMockForAbstractClass(DatabaseDriver::class);

        $databaseDriverMock->expects($this->once())
                           ->method('execute');

        (object) $domainMock = $this->createMock(DomainWithPreparationMethod::class);

        $domainMock->expects($this->at(0))
                   ->method('prepareForInsertion');

        $domainMock->expects($this->at(1))
                   ->method('getAvailableFields')
                   ->willReturn([]);

        $domainMock->expects($this->at(2))
                   ->method('getAvailableValues')
                   ->willReturn([]);

        $gateway->__construct($databaseDriverMock);

        $gateway->insert($domainMock);

    }

    /**
    * @dataProvider UpdateProvider
    */
    public function test_places_null_placeholder_when_value_is_null($dataToUpdate, $expectedStatement, $expectedPrepareParameters)
    {
        (object) $gateway = $this->getMockBuilder(Gateway::class)
                                 ->disableOriginalConstructor()
                                 ->onlyMethods(['model'])
                                 ->getMock();

        (object) $databaseTableMock = $this->getMockBuilder(DatabaseTable::class)
                                           ->disableOriginalConstructor()
                                           ->onlyMethods(['name', 'fields', 'changes'])
                                           ->getMock();

        $databaseTableMock->expects($this->once())
                          ->method('name')
                          ->willReturn('USERS');

        $databaseTableMock->expects($this->once())
                          ->method('fields')
                          ->willReturn([
                                'primary' => new DatabaseColumn('id',         'integer', 'NOT NULL UNIQUE AUTO_INCREMENT'),
                                             new DatabaseColumn('path',       'varchar(100)', 'NOT NULL UNIQUE'),
                                             new DatabaseColumn('name',       'varchar(190)'),
                                             new DatabaseColumn('date_added', 'datetime'),
                                             new DatabaseColumn('type_id', 'integer'),
                            ]);

        $databaseTableMock->__construct();

        $gateway->expects($this->exactly(2))
                ->method('model')
                ->willReturn([ 
                    'table' => $databaseTableMock,
                    'domain' => 'domain'
                ]);

        (object) $databaseDriverMock = $this->getMockForAbstractClass(DatabaseDriver::class);

        $databaseDriverMock->expects($this->exactly(1))
                           ->method('execute')
                           ->with(
                                $this->equalTo($expectedStatement),
                                $expectedPrepareParameters
                            );

        (object) $domainMock = $this->createMock(DomainWithPreparationMethod::class);

        $gateway->__construct($databaseDriverMock);

        $gateway->update($dataToUpdate);

    }
    
    public function UpdateProvider()
        {
            return [
                'non-registered field for the table gets ignored' => [
                    $dataToUpdate = [
                        'id' => 76,
                        'nonexistingfield' => '86',
                        'name' => 'rafael'
                    ],
                    $expectedStatement = "UPDATE users SET name = ? WHERE id = ? LIMIT 1",
                    $expectedPrepareParameters = ['rafael', 76]
                ],
                'non-registered field for the table gets ignored ----> injection attempt' => [
                    $dataToUpdate = [
                        'id' => 76,
                        "'; TRUNCATE DATABASE USERS;" => '86',
                        'name' => 'rafael'
                    ],
                    $expectedStatement = "UPDATE users SET name = ? WHERE id = ? LIMIT 1",
                    $expectedPrepareParameters = ['rafael', 76]
                ],
                'null value' => [
                    $dataToUpdate = [
                        'id' => 76,
                        'name' => null
                    ],
                    $expectedStatement = "UPDATE users SET name = NULL WHERE id = ? LIMIT 1",
                    $expectedPrepareParameters = [76]
                ],
                'empty string value' => [
                    $dataToUpdate = [
                        'id' => 76,
                        'name' => ''
                    ],
                    $expectedStatement = "UPDATE users SET name = ? WHERE id = ? LIMIT 1",
                    $expectedPrepareParameters = ['', 76]
                ],
                'empty integer value' => [
                    $dataToUpdate = [
                        'id' => 76,
                        'name' => 0
                    ],
                    $expectedStatement = "UPDATE users SET name = ? WHERE id = ? LIMIT 1",
                    $expectedPrepareParameters = [0, 76]
                ],
                'non empty string value' => [
                    $dataToUpdate = [
                        'id' => 76,
                        'name' => 'my name is...'
                    ],
                    $expectedStatement = "UPDATE users SET name = ? WHERE id = ? LIMIT 1",
                    $expectedPrepareParameters = ['my name is...', 76]
                ],
                'non empty integer value' => [
                    $dataToUpdate = [
                        'id' => 76,
                        'name' => 7 
                    ],
                    $expectedStatement = "UPDATE users SET name = ? WHERE id = ? LIMIT 1",
                    $expectedPrepareParameters = [7, 76]
                ],
            ];   
        }
            
}

