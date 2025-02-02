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

namespace CakeDC\Api\TestSuite;

use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\Utility\Hash;
use Cake\View\Exception\MissingTemplateException;
use CakeDC\Api\Service\ServiceRegistry;

/**
 * Class IntegrationTestCase
 *
 * @package CakeDC\Api\TestSuite
 */
class IntegrationTestCase extends \Cake\TestSuite\TestCase
{
    use IntegrationTestTrait;

    /**
     * @var array<array-key, string> Fixtures.
     */
    protected array $fixtures = [
        'plugin.CakeDC/Api.SocialAccounts',
        'plugin.CakeDC/Api.Users',
        'plugin.CakeDC/Api.Articles',
        'plugin.CakeDC/Api.Authors',
        'plugin.CakeDC/Api.Tags',
        'plugin.CakeDC/Api.ArticlesTags',
    ];

    /**
     * @var string|null Current logged in user
     */
    protected ?string $_defaultUserId = null;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        Configure::write('Api', []);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        ServiceRegistry::getServiceLocator()->clear();
    }

    /**
     * Default user api method.
     *
     * @param string|null $userId User id.
     * @return string|null
     */
    public function getDefaultUser(?string $userId = null)
    {
        if ($userId === null) {
            $userId = $this->_defaultUserId;
        } else {
            $this->_defaultUserId = $userId;
        }

        return $userId;
    }

    /**
     * Returns user token.
     *
     * @param string $userId User id.
     * @return mixed|null
     */
    protected function _userToken(?string $userId = null)
    {
        if ($userId === null) {
            $userId = $this->getDefaultUser();
        }
        $Users = TableRegistry::getTableLocator()->get('CakeDC/Users.Users');
        if ($userId) {
            $user = $Users->find()
                          ->where(['id' => $userId])
                          ->first();
            if ($user instanceof EntityInterface) {
                return $user['api_token'];
            }
        }

        return null;
    }

    /**
     * Send api request.
     *
     * @param string $url Url.
     * @param string $method HTTP method.
     * @param mixed $data Api parameters.
     * @param string $userId Current user id.
     * @param array $headers Headers list.
     * @return void
     * @throws \PHPUnit\Exception|\Throwable
     */
    public function sendRequest(
        string $url,
        string $method,
        $data = [],
        ?string $userId = null,
        array $headers = []
    ): void {
        ServiceRegistry::getServiceLocator()->clear();
        $userToken = $this->_userToken($userId);

        Configure::load('api');

        if (!empty($headers)) {
            $this->configRequest([
                ' ' => ' ',
                'headers' => $headers,
            ]);
        }

        if (!is_string($url)) {
            $this->_sendRequest($url, $method, $data);

            return;
        }
        $url = '/api' . $url;
        if ($userToken !== null) {
            $url = $this->_appendGetParam($url, 'token', (string)$userToken);
        }
        if ($method == 'GET' && !empty($data)) {
            foreach ($data as $key => $value) {
                if (!is_array($value)) {
                    $url = $this->_appendGetParam($url, $key, (string)$value);
                }
            }
        }
        try {
            ServiceRegistry::getServiceLocator()->clear();
            TableRegistry::getTableLocator()->clear();
            $this->_sendRequest($url, $method, $data);
        } catch (MissingTemplateException $ex) {
            $message = sprintf('Possibly related to %s', $this->_exception->getMessage());
            throw new MissingTemplateException($message, [], 500, $ex);
        }
    }

    /**
     * Add param to request.
     *
     * @param string $url Url.
     * @param string $key Param name.
     * @param string $value Param value.
     * @return string
     */
    protected function _appendGetParam(string $url, string $key, string $value): string
    {
        $appendChar = strpos($url, '?') !== false ? '&' : '?';

        return $url . $appendChar . urlencode($key) . '=' . urlencode($value);
    }

    /**
     * Assert result is success.
     *
     * @param array|null $result Response.
     * @return void
     */
    public function assertSuccess($result): void
    {
        $this->assertTrue(is_array($result));
        $this->assertEquals('success', $result['status']);
        $this->assertEquals(200, $this->_response->getStatusCode());
    }

    /**
     * @return mixed
     */
    public function getJsonResponse()
    {
        $body = (string)$this->_response->getBody();

        return json_decode((string)$this->_response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Assert result is error.
     *
     * @param mixed $result Response.
     * @param int $code Result code.
     * @return void
     */
    public function assertError($result, ?int $code = null): void
    {
        $this->assertTrue(is_array($result));
        $this->assertEquals('error', $result['status']);
        $this->assertEquals(200, $this->_response->getStatusCode());
        if (!empty($code)) {
            $this->assertEquals($code, $result['code']);
        }
    }

    /**
     * Helper method for status assertions.
     *
     * @param int $code Status code.
     * @param string $message The error message.
     * @return void
     */
    public function assertStatus($code, $message = null)
    {
        if ($message === null) {
            $message = "Status code $code does not match";
        }
        $this->assertResponseCode($code, $message);
    }

    /**
     * Assert error message.
     *
     * @param array $result Response.
     * @param string $expectedMessage Message.
     * @return void
     */
    public function assertErrorMessage(array $result, string $expectedMessage): void
    {
        $message = Hash::get($result, 'message');
        $this->assertTrue(is_string($message) && strpos($message, $expectedMessage) === 0);
    }
}
