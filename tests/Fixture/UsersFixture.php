<?php
declare(strict_types=1);
/**
 * Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Api\Test\Fixture;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Authentication\PasswordHasher\PasswordHasherFactory;
use Cake\TestSuite\Fixture\TestFixture;
use CakeDC\Users\Webauthn\Base64Utility;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
{

    /**
     * records property
     *
     * @var array
     */
    public array $records = [];

    /**
     * Init method
     *
     * @return void
     */
    public function __construct()
    {
        $this->records = [
            [
                'id' => '00000000-0000-0000-0000-000000000001',
                'username' => 'user-1',
                'email' => 'user-1@test.com',
                'password' => '12345',
                'first_name' => 'first1',
                'last_name' => 'last1',
                'token' => 'ae93ddbe32664ce7927cf0c5c5a5e59d',
                'token_expires' => '2035-06-24 17:33:54',
                'api_token' => 'yyy',
                'activation_date' => '2015-06-24 17:33:54',
                'secret' => 'yyy',
                'secret_verified' => false,
                'tos_date' => '2015-06-24 17:33:54',
                'active' => true,
                'is_superuser' => true,
                'role' => 'admin',
                'created' => '2015-06-24 17:33:54',
                'modified' => '2015-06-24 17:33:54',
                'last_login' => '2015-06-24 17:33:54',
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000002',
                'username' => 'user-2',
                'email' => 'user-2@test.com',
                'password' => '12345',
                'first_name' => 'user',
                'last_name' => 'second',
                'token' => '6614f65816754310a5f0553436dd89e9',
                'token_expires' => '2015-06-24 17:33:54',
                'api_token' => 'xxx',
                'activation_date' => '2015-06-24 17:33:54',
                'secret' => 'xxx',
                'secret_verified' => false,
                'tos_date' => '2015-06-24 17:33:54',
                'active' => true,
                'is_superuser' => true,
                'role' => 'admin',
                'created' => '2015-06-24 17:33:54',
                'modified' => '2015-06-24 17:33:54',
                'last_login' => '2015-06-24 17:33:54',
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000003',
                'username' => 'user-3',
                'email' => 'user-3@test.com',
                'password' => '12345',
                'first_name' => 'user',
                'last_name' => 'third',
                'token' => 'token-3',
                'token_expires' => '2030-06-20 17:33:54',
                'api_token' => 'xxx',
                'activation_date' => '2015-06-24 17:33:54',
                'secret' => 'xxx',
                'secret_verified' => true,
                'is_superuser' => true,
                'tos_date' => '2015-06-24 17:33:54',
                'active' => false,
                'role' => 'admin',
                'created' => '2015-06-24 17:33:54',
                'modified' => '2015-06-24 17:33:54',
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000004',
                'username' => 'user-4',
                'email' => '4@example.com',
                'password' => '$2y$10$Nvu7ipP.z8tiIl75OdUvt.86vuG6iKMoHIOc7O7mboFI85hSyTEde',
                'first_name' => 'FirstName4',
                'last_name' => 'Lorem ipsum dolor sit amet',
                'token' => 'token-4',
                'token_expires' => '2030-06-24 17:33:54',
                'api_token' => 'Lorem ipsum dolor sit amet',
                'activation_date' => '2015-06-24 17:33:54',
                'secret' => 'Lorem ipsum dolor sit amet',
                'secret_verified' => true,
                'is_superuser' => false,
                'tos_date' => '2015-06-24 17:33:54',
                'active' => true,
                'role' => 'Lorem ipsum dolor sit amet',
                'created' => '2015-06-24 17:33:54',
                'modified' => '2015-06-24 17:33:54',
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000005',
                'username' => 'user-5',
                'email' => 'test@example.com',
                'password' => '12345',
                'first_name' => 'first-user-5',
                'last_name' => 'firts name 5',
                'token' => 'token-5',
                'token_expires' => '2015-06-24 17:33:54',
                'api_token' => '',
                'activation_date' => '2015-06-24 17:33:54',
                'secret' => '',
                'secret_verified' => false,
                'tos_date' => '2015-06-24 17:33:54',
                'active' => true,
                'is_superuser' => false,
                'role' => 'user',
                'created' => '2015-06-24 17:33:54',
                'modified' => '2015-06-24 17:33:54',
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000006',
                'username' => 'user-6',
                'email' => '6@example.com',
                'password' => '12345',
                'first_name' => 'first-user-6',
                'last_name' => 'firts name 6',
                'token' => 'token-6',
                'token_expires' => '2045-06-24 17:33:54',
                'api_token' => '',
                'activation_date' => '2015-06-24 17:33:54',
                'secret' => '',
                'secret_verified' => false,
                'tos_date' => '2015-06-24 17:33:54',
                'active' => false,
                'is_superuser' => false,
                'role' => 'user',
                'created' => '2015-06-24 17:33:54',
                'modified' => '2015-06-24 17:33:54',
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000007',
                'username' => 'Lorem ipsum dolor sit amet',
                'email' => 'Lorem ipsum dolor sit amet',
                'password' => 'Lorem ipsum dolor sit amet',
                'first_name' => 'Lorem ipsum dolor sit amet',
                'last_name' => 'Lorem ipsum dolor sit amet',
                'token' => 'Lorem ipsum dolor sit amet',
                'token_expires' => '2015-06-24 17:33:54',
                'api_token' => 'Lorem ipsum dolor sit amet',
                'activation_date' => '2015-06-24 17:33:54',
                'secret' => 'Lorem ipsum dolor sit amet',
                'secret_verified' => false,
                'tos_date' => '2015-06-24 17:33:54',
                'active' => true,
                'is_superuser' => false,
                'role' => 'Lorem ipsum dolor sit amet',
                'created' => '2015-06-24 17:33:54',
                'modified' => '2015-06-24 17:33:54',
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000008',
                'username' => 'Lorem ipsum dolor sit amet',
                'email' => 'Lorem ipsum dolor sit amet',
                'password' => 'Lorem ipsum dolor sit amet',
                'first_name' => 'Lorem ipsum dolor sit amet',
                'last_name' => 'Lorem ipsum dolor sit amet',
                'token' => 'Lorem ipsum dolor sit amet',
                'token_expires' => '2015-06-24 17:33:54',
                'api_token' => 'Lorem ipsum dolor sit amet',
                'activation_date' => '2015-06-24 17:33:54',
                'secret' => 'Lorem ipsum dolor sit amet',
                'secret_verified' => false,
                'tos_date' => '2015-06-24 17:33:54',
                'active' => true,
                'is_superuser' => false,
                'role' => 'Lorem ipsum dolor sit amet',
                'created' => '2015-06-24 17:33:54',
                'modified' => '2015-06-24 17:33:54',
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000009',
                'username' => 'Lorem ipsum dolor sit amet',
                'email' => 'Lorem ipsum dolor sit amet',
                'password' => 'Lorem ipsum dolor sit amet',
                'first_name' => 'Lorem ipsum dolor sit amet',
                'last_name' => 'Lorem ipsum dolor sit amet',
                'token' => 'Lorem ipsum dolor sit amet',
                'token_expires' => '2015-06-24 17:33:54',
                'api_token' => 'Lorem ipsum dolor sit amet',
                'activation_date' => '2015-06-24 17:33:54',
                'secret' => 'Lorem ipsum dolor sit amet',
                'secret_verified' => false,
                'tos_date' => '2015-06-24 17:33:54',
                'active' => true,
                'is_superuser' => false,
                'role' => 'Lorem ipsum dolor sit amet',
                'created' => '2015-06-24 17:33:54',
                'modified' => '2015-06-24 17:33:54',
            ],
        ];

        parent::__construct();
        $hasher = PasswordHasherFactory::build(DefaultPasswordHasher::class);
        foreach ($this->records as $id => $record) {
            $this->records[$id]['password'] = $hasher->hash($record['password']);
        }
    }
}
