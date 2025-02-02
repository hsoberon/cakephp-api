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

namespace CakeDC\Api\Service\Action\Extension\Auth;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use CakeDC\Api\Service\Action\Extension\Extension;
use CakeDC\Users\Controller\Traits\CustomUsersTableTrait;

/**
 * Class UserFormattingExtension
 *
 * @package CakeDC\Api\Service\Action\Extension\Auth
 */
class UserFormattingExtension extends Extension implements EventListenerInterface
{
    use CustomUsersTableTrait;

    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Action.Auth.onLoginFormat' => 'onLoginFormat',
            'Action.Auth.onRegisterFormat' => 'onRegisterFormat',
        ];
    }

    /**
     * On Login Format.
     *
     * @param \Cake\Event\Event $event An Event instance
     * @return array|null
     */
    public function onLoginFormat(Event $event): ?array
    {
        return $this->_userCleanup($event->getData('user'));
    }

    /**
     * On Register Format.
     *
     * @param \Cake\Event\Event $event An Event instance
     * @return array|null
     */
    public function onRegisterFormat(Event $event): ?array
    {
        return $this->_userCleanup($event->getData('user'));
    }

    /**
     * @param array|\Cake\Datasource\EntityInterface|null $user User data
     * @return array|null
     */
    protected function _userCleanup($user): ?array
    {
        if ($user === null) {
            return null;
        }

        $currentUser = $this
            ->getUsersTable()
            ->find()
            ->where([$this->getUsersTable()->aliasField('id') => $user['id']])
            ->first();

        if ($currentUser === null) {
            return null;
        }

        $user = $currentUser->toArray();
        $user['api_token'] = $currentUser['api_token'];

        $cleanup = ['created', 'modified', 'is_superuser'];
        foreach ($cleanup as $field) {
            unset($user[$field]);
        }

        return $user;
    }
}
