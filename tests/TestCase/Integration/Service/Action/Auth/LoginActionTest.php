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

namespace CakeDC\Api\Test\TestCase\Integration\Service\Action\Auth;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use CakeDC\Api\Test\ConfigTrait;
use CakeDC\Api\TestSuite\IntegrationTestCase;

/**
 * Class LoginActionTest
 *
 * @package CakeDC\Api\Test\TestCase\Integration\Service\Action\Auth
 */
class LoginActionTest extends IntegrationTestCase
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
        $this->_authAccess();
        Configure::write('App.fullBaseUrl', 'http://example.com');
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

    public function testSuccessLogin()
    {
        $this->sendRequest('/auth/login', 'POST', ['username' => 'user-1', 'password' => '12345']);
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);
        $expected = [
            'id' => '00000000-0000-0000-0000-000000000001',
            'username' => 'user-1',
            'email' => 'user-1@test.com',
            'first_name' => 'first1',
            'last_name' => 'last1',
            'active' => true,
            'api_token' => 'yyy',
            'role' => 'admin',
            'secret_verified' => false,
            'last_login' => '2015-06-24T17:33:54+00:00',
        ];
        $data = Hash::get($result, 'data');
        unset($data['activation_date']);
        unset($data['tos_date']);
        $this->assertEquals($expected, $data);
    }

    public function testLoginFail()
    {
        $this->sendRequest('/auth/login', 'POST', ['username' => 'user-1', 'password' => '111']);
        $result = $this->getJsonResponse();
        $this->assertError($result, 401);
        $this->assertErrorMessage($result, 'User not found');
    }
}
