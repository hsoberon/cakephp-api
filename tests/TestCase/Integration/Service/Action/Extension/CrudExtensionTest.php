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
use CakeDC\Api\Test\ConfigTrait;
use CakeDC\Api\Test\Settings;
use CakeDC\Api\TestSuite\IntegrationTestCase;

class CrudExtensionTest extends IntegrationTestCase
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
        $this->_loadDefaultExtensions([]);
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
        Configure::write('Test.Api.Extension', null);
    }

    public function testIndex()
    {
        $this->sendRequest('/articles', 'GET');
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);
        $this->assertEquals(15, is_countable($result['data']) ? count($result['data']) : 0);
    }

    public function testView()
    {
        $this->sendRequest('/articles/1', 'GET');
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);
        $article = [
            'id' => 1,
            'author_id' => 1,
            'title' => 'First Article',
            'body' => 'First Article Body',
            'published' => 'Y',
        ];
        $this->assertEquals($article, $result['data']);
    }

    public function testAdd()
    {
        $article = [
            'author_id' => 15,
            'title' => 'New Article',
            'body' => 'New Article Body',
            'published' => 'Y',
        ];
        $this->sendRequest('/articles', 'POST', $article);
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);
        $this->assertArrayHasKey('id', $result['data']);
        $id = $result['data']['id'];

        $this->sendRequest('/articles/' . $id, 'GET');
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);

        $res = array_intersect_key($article, $result['data']);
        $this->assertEquals($article, $res);
    }

    public function testEdit()
    {
        $article = [
            'author_id' => 15,
            'title' => 'New Article',
            'body' => 'New Article Body',
            'published' => 'Y',
        ];
        $this->sendRequest('/articles/1', 'PUT', $article);
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);
        $this->assertArrayHasKey('id', $result['data']);
        $this->assertEquals(1, $result['data']['id']);

        $this->sendRequest('/articles/1', 'GET');
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);

        $res = array_intersect_key($article, $result['data']);
        $this->assertEquals($article, $res);
    }

    public function testDelete()
    {
        $this->sendRequest('/articles/1', 'DELETE');
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);

        $this->sendRequest('/articles/1', 'GET');
        $result = $this->getJsonResponse();
        $this->assertError($result, 404);
    }
}
