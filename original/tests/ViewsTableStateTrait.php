<?php

namespace CouponURLs\Original\Tests;

use CouponURLs\Original\Environment\Env;
use PHPUnit_Extensions_Database_DataSet_YamlDataSet as YamlDataSet;

Trait ViewsTableStateTrait
{
    protected function confirmViewsTableState($numberOfRows, $AfterInsertDatasetFile)
    {
        $actualTable = $this->getConnection()->createQueryTable(
            'couponsplus_url_views', 'SELECT id, destination_id, device_type, device_name, device_os, device_browser, device_browser_version, device_user_agent, device_referer FROM couponsplus_url_views'
        );

        $expectedTable = (new YamlDataSet(Env::testsDirectory("integration/datasets").$AfterInsertDatasetFile))
                              ->getTable("couponsplus_url_views");

        $this->assertEquals($numberOfRows, $this->getConnection()->getRowCount('couponsplus_url_views'));
        if ($numberOfRows !== 0) {
            $this->assertTablesEqual($expectedTable, $actualTable);   
        }
    }
}