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

/**
 * Class CorsExtensionTest
 *
 * @package CakeDC\Api\Test\TestCase\Integration\Service\Extension
 */
class CorsExtensionTest extends IntegrationTestCase
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
        $this->_loadDefaultExtensions('CakeDC/Api.Cors');
        $this->_loadDefaultExtensions('CakeDC/Api.Paginate');
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

    public function testCorsHeaders()
    {
        $this->_request['headers']['Origin'] = 'http://foobar.com';
        $this->sendRequest('/authors', 'GET', ['limit' => 4, 'sort' => 'id']);
        $result = $this->getJsonResponse();
        $headers = $this->_response->getHeaders();
        $this->assertSuccess($result);
        $this->assertTrue(!empty($headers));
        $this->assertEquals(['*'], $headers['Access-Control-Allow-Origin']);
        $expectedMethods = ['GET, POST, PUT, DELETE, OPTIONS, HEAD, PATCH'];
        $this->assertEquals($expectedMethods, $headers['Access-Control-Allow-Methods']);
        $expectedHeaders = ['X-CSRF-Token, Content-Type, Access-Control-Allow-Headers, Access-Control-Allow-Origin, Authorization, X-Requested-With'];
        $this->assertEquals($expectedHeaders, $headers['Access-Control-Allow-Headers']);
        $this->assertEquals(['true'], $headers['Access-Control-Allow-Credentials']);
        $this->assertEquals([300], $headers['Access-Control-Max-Age']);
    }
}
