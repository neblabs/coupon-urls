<?php

namespace CouponURLs\Original\Tests;

use CouponURLs\App\Data\Schema\ApplicationDatabase;
use CouponURLs\Original\Data\Drivers\DatabaseDriver;
use CouponURLs\Original\Data\Drivers\WordPressDatabaseDriver;
use CouponURLs\Original\Environment\Env;
use CouponURLs\Original\Tests\ArrayDataSet;
use CouponURLs\Original\Tests\ResetAutoincrementOperation;
use PHPUnit_Extensions_Database_DataSet_YamlDataSet as YamlDataSet;
use PHPUnit_Extensions_Database_TestCase;

Abstract Class DatabaseTestCase extends PHPUnit_Extensions_Database_TestCase
{
    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    protected static $installCustomTables = false;
    protected $installCustomTablesOnEachTest = false;

    protected $useDatabase = true;

    protected $testSubDirectory = 'unit';
    protected $dataSetFile = 'empty.yml';

    protected static function getCredentials()
    {
        return null;   
    }

    public static function setUpBeforeClass()
    {
        //print "\n".'Running class: ' . get_called_class() ."\n";
        if (static::$installCustomTables) {
            (object) $applicationDatabase = new ApplicationDatabase(
                new WordPressDatabaseDriver(static::getCredentials())
            );

            $applicationDatabase->install();
        }  
    }

    public static function tearDownAfterClass()
    {
        if (static::$installCustomTables) {
            (object) $applicationDatabase = new ApplicationDatabase(
                new WordPressDatabaseDriver(static::getCredentials())
            );

            $applicationDatabase->uninstall();
        }  
    }

    protected function setUp()
    {
        //var_dump($this->getName());
        if ($this->installCustomTablesOnEachTest) {
            (object) $applicationDatabase = new ApplicationDatabase(
                new WordPressDatabaseDriver(static::getCredentials())
            );
            $applicationDatabase->install();
        }  

        $this->resetWordPressState();
        
        parent::setUp();
    }

    protected function tearDown()
    {
        $this->resetWordPressState();

        if ($this->installCustomTablesOnEachTest) {
            (object) $applicationDatabase = new ApplicationDatabase(
                new WordPressDatabaseDriver(static::getCredentials())
            );

            $applicationDatabase->uninstall();
        }  

        parent::tearDown();
    }

    protected function resetWordPressState()
    {
        global $wp_object_cache;

        $wp_object_cache->flush();

        $GLOBALS['post'] = null;   

        setup_postdata(null);
    }

    protected function getDataSet()
    {
        return new YamlDataSet(Env::testsDirectory("{$this->testSubDirectory}/datasets").$this->dataSetFile);
    }

    public function getConnection()
    {
        if ($this->conn === null) {
            
            if (self::$pdo == null) {
                self::$pdo = new \PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'], [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
            }

            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }

        return $this->conn;
    }

    protected function insertTable($yamlFileOrArraySet)
    {
        (object) $dataSet = $this->getYAMLOrArrayDataSet($yamlFileOrArraySet);

        \PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT()
        ->execute($this->getConnection(), $dataSet);   
    }

    protected function tryTableState($table, $fieldsToCompare = '*', $expectedTableFileOrArraySet, $expectedNumberOfRows = null, $orderby = 'id', Array $extraCheckData = [])
    {

        $WHERE = '';
        
        if (isset($extraCheckData['WHERE'])) {
            $WHERE = "WHERE {$extraCheckData['WHERE']}";
        }

        $orderby = isset($extraCheckData['orderBy'])? $extraCheckData['orderBy'] : $orderby;

        $actualTable = $this->getConnection()->createQueryTable(
            $table, "SELECT $fieldsToCompare FROM $table {$WHERE} ORDER BY $orderby"
        );

        (object) $expectedTableDataSet = $this->getYAMLOrArrayDataSet($expectedTableFileOrArraySet);

        $expectedTable = $expectedTableDataSet->getTable($table);

        if ($expectedNumberOfRows !== null) {
            $this->assertEquals($expectedNumberOfRows, $actualTable->getRowCount($table));
        }

        if ($expectedTable->getRowCount($table) > 0) {
            $this->assertTablesEqual($expectedTable, $actualTable, $this->getTablesData($expectedTable, $actualTable));
        }
        
    }

    protected function getYAMLOrArrayDataSet($yamlFileOrArraySet)
    {
        return is_array($yamlFileOrArraySet)? new ArrayDataSet($yamlFileOrArraySet): new YamlDataSet($this->getTableFileFrom($yamlFileOrArraySet));
    }
    

    protected function getTablesData($expectedTable, $actualTable)
    {
        (integer) $rowCount = $expectedTable->getRowCount() > $actualTable->getRowCount()? $expectedTable->getRowCount() : $actualTable->getRowCount();
        
        foreach (range(1, $rowCount) as $index) {
            $index -= 1;
            if (!($expectedTable->getRowCount() <= $index)) {
                foreach ($expectedTable->getRow($index) as $key => $value) {
                    if ($actualTable->getRow($index)[$key] != $value) {
                        return "\n___\n".
                               "\nexpected $key:{$value}\n".
                               "actual   $key:{$actualTable->getRow($index)[$key]}\n".PHP_EOL;
                    }
                }
            }
        }
    }
    
    protected function getSetUpOperation()
    {
        if ($this->useDatabase) {
            return new \PHPUnit_Extensions_Database_Operation_Composite(array(
                \PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL(),
                \PHPUnit_Extensions_Database_Operation_Factory::INSERT(),
            ));
        }

        return \PHPUnit_Extensions_Database_Operation_Factory::NONE();
    }

    protected function getTearDownOperation()
    {
        //return \PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL();
        return \PHPUnit_Extensions_Database_Operation_Factory::TRUNCATE();
    }

    protected function getTableFileFrom($tableFile)
    {
        return file_exists($tableFile)? $tableFile : 
                    Env::testsDirectory("{$this->testSubDirectory}/datasets").$tableFile;   
    }
}