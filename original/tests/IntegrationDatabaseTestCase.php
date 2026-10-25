<?php

namespace CouponURLs\Original\Tests;

use GuzzleHttp\Client;
use CouponURLs\App\Credentials\IntegrationTestingCredentials;
use CouponURLs\Original\Tests\DatabaseTestCase;

Class IntegrationDatabaseTestCase extends DatabaseTestCase
{
    protected static $http;
    protected $testSubDirectory = 'integration';
    
    protected static function getCredentials()
    {
        return new IntegrationTestingCredentials;   
    }

    public function setUp()
    {
        if (!(static::$http instanceof Client)) {
            static::$http = new Client([
                'base_uri' => 'http://localhost/couponsplus/',
                'http_errors' => false,
                'allow_redirects' => false,
            ]);
        }

        parent::setUp();
    }

    protected function tryRequest($path, $userAgent, $responseCode, $redirectionUrl, $assertHasHeader, Array $headers = [], $responseShouldHaveTheseHeaders = [], Callable $debug = null)
    {
        (object) $response = $this->getResponse($path, $userAgent, $headers);

        if ($debug) {$debug($response); exit();}

        $this->assertEquals(
            $responseCode, 
            $response->getStatusCode(), 
            $responseCode != $response->getStatusCode()? $response->getBody()->getContents() : ''
        );

        $responseShouldHaveTheseHeaders = array_merge([
            'expectedHeaders' => [],
            'shouldNotHaveTheseHeaders' => []
        ], $responseShouldHaveTheseHeaders);

        foreach ($responseShouldHaveTheseHeaders['expectedHeaders'] as $expectedHeaderName => $expectedHeaderValue) {
            $this->assertEquals($expectedHeaderValue, $response->getHeader($expectedHeaderName)[0], "Error with expected header: {$expectedHeaderName}"); 
        }
        if ($responseShouldHaveTheseHeaders['shouldNotHaveTheseHeaders']) {
            $this->assertTrue(
                array_diff(
                    $responseShouldHaveTheseHeaders['shouldNotHaveTheseHeaders'], 
                    array_keys($response->getHeaders())
                ) === $responseShouldHaveTheseHeaders['shouldNotHaveTheseHeaders'],
                "It seems the response has headers we are not expecting "
            );
        }

        $this->{$assertHasHeader}($response->hasHeader('Location'));
        $this->assertEquals($redirectionUrl, isset($response->getHeader('Location')[0])? $response->getHeader('Location')[0] : '');   
    }

    protected function getResponse(string $path, $userAgent, array $headers = [])
    {
        return static::$http->request('GET', $path, [
            'headers' => array_merge([
                'User-Agent' => $userAgent
            ], $headers),
            'curl' => [
                CURLOPT_FRESH_CONNECT => true
            ]
        ]);
    }

    protected function tryResponseContent(string $path, string $userAgent, array $headers = [], int $expectedResponseCode = 200, array $expectedPartialContent, array $contentNotExpected = ['Fatal error', 'warning', 'notice'], bool $hasLocationHeader = false)
    {
        (object) $response = $this->getResponse($path, $userAgent, $headers);

        (string) $responseContent = $response->getbody()->getcontents();

        $this->assertEquals($expectedResponseCode, $response->getStatusCode(), $responseContent);
        $this->assertSame($hasLocationHeader, $response->hasHeader('Location'));

        $responseContent = str_replace("\n\t", '', strtolower($responseContent));

        foreach ($expectedPartialContent as $expectedContent) {
            $this->assertContains(strtolower($expectedContent), strtolower($responseContent), $expectedContent);
        }

        $contentNotExpected = array_filter($contentNotExpected);

        foreach ($contentNotExpected as $contentNotExpectedSingle) {
            $this->assertNotContains(strtolower($contentNotExpectedSingle), strtolower($responseContent), $contentNotExpectedSingle);
        }
    }

    protected function tryActivatePlugin($pluginFileName)
    {
        $this->tryPluginManagement([
            'type' => 'ActivatePlugin',
            'fileName' => $pluginFileName,
            'shouldBeActivated' => 'true'
        ]);
    }

    protected function tryDeactivatePlugin($pluginFileName)
    {
        $this->tryPluginManagement([
            'type' => 'DeActivatePlugin',
            'fileName' => $pluginFileName,
            'shouldBeActivated' => 'false'
        ]);
    }

    public function tryPluginManagement(Array $plugin)
    {
        (object) $activateResponse = static::$http->request('GET', 'http://localhost/couponsplus/', [
            'headers' => [
                'Neblabs_bridge' => $plugin['type'],
                'Neblabs_bridge_argument' => $plugin['fileName']
            ]
        ]); 

        $this->tryActivatedPlugin(
            $plugin['fileName'], 
            $plugin['shouldBeActivated']
        );   
    }

    protected function tryActivatedPlugin($pluginFile, $shouldBeActivated)
    {
        (object) $checkActivateResponse = static::$http->request('GET', 'http://localhost/couponsplus/', [
            'headers' => [
                'Neblabs_bridge' => 'CheckActivatedPlugin',
                'Neblabs_bridge_argument' => $pluginFile
            ]
        ]);

        (string) $pluginIsActive = $checkActivateResponse->getbody()->getcontents();
        $this->assertSame($shouldBeActivated, $pluginIsActive, "checking plugin activation | Expected {$shouldBeActivated}, Actual: {$pluginIsActive}");   
    }
}