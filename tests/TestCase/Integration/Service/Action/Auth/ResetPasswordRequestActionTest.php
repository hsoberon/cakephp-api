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
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use CakeDC\Api\Test\ConfigTrait;
use CakeDC\Api\Test\Settings;
use CakeDC\Api\TestSuite\IntegrationTestCase;

/**
 * Class ResetPasswordRequestActionTest
 *
 * @package CakeDC\Api\Test\TestCase\Integration\Service\Action\Auth
 */
class ResetPasswordRequestActionTest extends IntegrationTestCase
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

    public function testSuccessResetPasswordRequest()
    {
        $builder = Router::createRouteBuilder('/', []);
        $builder->connect('/users/reset-password/*', [
            'plugin' => 'CakeDC/Users',
            'controller' => 'Users',
            'action' => 'resetPassword',
        ]);
        $this->sendRequest('/auth/reset_password_request', 'POST', ['reference' => 'user-1']);
        $result = $this->getJsonResponse();
        $this->assertSuccess($result);
        $this->assertTextEquals('Please check your email to continue with password reset process', $result['data']);

        $Users = TableRegistry::getTableLocator()->get('CakeDC/Users.Users');
        $user = $Users->find()->where(['id' => Settings::USER1])->enableHydration(false)->first();
        $this->assertNotEmpty($user['token']);
    }
}
