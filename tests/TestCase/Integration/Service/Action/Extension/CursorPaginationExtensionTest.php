<?php
declare(strict_types=1);

/**
 * Copyright 2016 - 2019, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2016 - 2019, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Api\Test\TestCase\Integration\Service\Action\Extension;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use CakeDC\Api\Test\ConfigTrait;
use CakeDC\Api\Test\Settings;
use CakeDC\Api\TestSuite\IntegrationTestCase;

class CursorPaginationExtensionTest extends IntegrationTestCase
{
    use ConfigTrait;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        Configure::write('App.fullBaseUrl', 'http://example.com');
        $this->_tokenAccess();
        $this->_loadDefaultExtensions('CakeDC/Api.CursorPaginate');
        $this->getDefaultUser(Settings::USER1);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testListDefault()
    {
        $this->sendRequest('/articles', 'GET');
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);
        $this->assertEquals(20, Hash::get($result, 'pagination.count'));
        $this->assertEquals(null, Hash::get($result, 'pagination.since_id'));
        $this->assertEquals(null, Hash::get($result, 'pagination.max_id'));
        $this->assertEquals('http://example.com/api/articles?since_id=15', Hash::get($result, 'pagination.links.prev'));
        $this->assertEquals('http://example.com/api/articles?max_id=1', Hash::get($result, 'pagination.links.next'));
    }

    public function testListWithConfigCount()
    {
        $this->_loadDefaultExtensions([
            'CakeDC/Api.CursorPaginate' => [
                'defaultCount' => 5,
            ],
        ], true);
//        Configure::write('Test.Api.Extension', [
//            'default' => [
//                'CakeDC/Api.CursorPaginate' => [
//                    'defaultCount' => 5
//                ]
//            ]
//        ]);

        $this->sendRequest('/articles', 'GET');
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);
        $this->assertEquals(5, Hash::get($result, 'pagination.count'));
        $this->assertEquals(null, Hash::get($result, 'pagination.since_id'));
        $this->assertEquals(null, Hash::get($result, 'pagination.max_id'));
        $this->assertEquals('http://example.com/api/articles?since_id=15', Hash::get($result, 'pagination.links.prev'));
        $this->assertEquals('http://example.com/api/articles?max_id=11', Hash::get($result, 'pagination.links.next'));
    }

    public function testListWithCountAsGetParam()
    {
        $this->sendRequest('/articles', 'GET', ['count' => 5]);
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);
        $this->assertEquals(5, Hash::get($result, 'pagination.count'));
        $this->assertEquals(null, Hash::get($result, 'pagination.since_id'));
        $this->assertEquals(null, Hash::get($result, 'pagination.max_id'));
        $this->assertEquals('http://example.com/api/articles?since_id=15', Hash::get($result, 'pagination.links.prev'));
        $this->assertEquals('http://example.com/api/articles?max_id=11', Hash::get($result, 'pagination.links.next'));
    }

    public function testListWithMaxId()
    {
        $this->sendRequest('/articles', 'GET', ['count' => 5, 'max_id' => 11]);
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);
        $this->assertEquals(5, Hash::get($result, 'pagination.count'));
        $this->assertEquals(null, Hash::get($result, 'pagination.since_id'));
        $this->assertEquals(11, Hash::get($result, 'pagination.max_id'));
        $this->assertEquals('http://example.com/api/articles?since_id=10', Hash::get($result, 'pagination.links.prev'));
        $this->assertEquals('http://example.com/api/articles?max_id=6', Hash::get($result, 'pagination.links.next'));
    }

    public function testListWithMaxIdWithNewDataAdded()
    {
        $this->_addData(10);
        $this->sendRequest('/articles', 'GET', ['count' => 5, 'max_id' => 11]);
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);
        $this->assertEquals(5, Hash::get($result, 'pagination.count'));
        $this->assertEquals(null, Hash::get($result, 'pagination.since_id'));
        $this->assertEquals(11, Hash::get($result, 'pagination.max_id'));
        $this->assertEquals('http://example.com/api/articles?since_id=10', Hash::get($result, 'pagination.links.prev'));
        $this->assertEquals('http://example.com/api/articles?max_id=6', Hash::get($result, 'pagination.links.next'));
    }

    public function testListWithCountAsGetParamWithNewDataAdded()
    {
        $this->_addData(10);
        $this->sendRequest('/articles', 'GET', ['count' => 5]);
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);
        $this->assertEquals(5, Hash::get($result, 'pagination.count'));
        $this->assertEquals(null, Hash::get($result, 'pagination.since_id'));
        $this->assertEquals(null, Hash::get($result, 'pagination.max_id'));
        $this->assertEquals('http://example.com/api/articles?since_id=25', Hash::get($result, 'pagination.links.prev'));
        $this->assertEquals('http://example.com/api/articles?max_id=21', Hash::get($result, 'pagination.links.next'));
    }

    public function testListWithSinceId()
    {
        $this->sendRequest('/articles', 'GET', ['count' => 5, 'since_id' => 15]);
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);
        $this->assertEquals(5, Hash::get($result, 'pagination.count'));
        $this->assertEquals(15, Hash::get($result, 'pagination.since_id'));
        $this->assertEquals(null, Hash::get($result, 'pagination.max_id'));
        $this->assertEquals('http://example.com/api/articles?since_id=15', Hash::get($result, 'pagination.links.prev'));
        $this->assertEquals('http://example.com/api/articles', Hash::get($result, 'pagination.links.next'));
    }

    public function testListWithSinceIdWithNewDataAdded()
    {
        $this->_addData(10);
        $this->sendRequest('/articles', 'GET', ['count' => 5, 'since_id' => 15]);
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);
        $this->assertEquals(5, Hash::get($result, 'pagination.count'));
        $this->assertEquals(15, Hash::get($result, 'pagination.since_id'));
        $this->assertEquals(null, Hash::get($result, 'pagination.max_id'));
        $this->assertEquals('http://example.com/api/articles?since_id=20', Hash::get($result, 'pagination.links.prev'));
        $this->assertEquals('http://example.com/api/articles?max_id=16', Hash::get($result, 'pagination.links.next'));
    }

    protected function _addData($count)
    {
        $Article = TableRegistry::getTableLocator()->get('Articles');
        $Article->createRecords($count, 1);
    }
}
