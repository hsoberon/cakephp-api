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

namespace CakeDC\Api\Service\Action\Extension;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;

/**
 * Class NestedExtension
 *
 * @package CakeDC\Api\Service\Action\Extension
 */
class NestedExtension extends Extension implements EventListenerInterface
{
    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Action.Crud.onFindEntities' => 'findEntities',
            'Action.Crud.onFindEntity' => 'findEntity',
            'Action.Crud.onPatchEntity' => 'patchEntity',
        ];
    }

    /**
     * On find entities.
     *
     * @param \Cake\Event\Event $event An Event instance
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findEntities(Event $event): \Cake\ORM\Query\SelectQuery
    {
        /** @var \CakeDC\Api\Service\Action\CrudAction $action */
        $action = $event->getSubject();
        /** @var \Cake\ORM\Query\SelectQuery $query */
        $query = $event->getData('query');
        $foreignKey = $action->getParentId();
        $field = $action->getParentIdName();
        if ($field !== null) {
            $query->where([$field => $foreignKey]);
        }
        if ($event->getResult()) {
            $query = $event->getResult();
        }

        return $query;
    }

    /**
     * On find entity.
     *
     * @param \Cake\Event\Event $event An Event instance
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findEntity(Event $event): \Cake\ORM\Query\SelectQuery
    {
        /** @var \CakeDC\Api\Service\Action\CrudAction $action */
        $action = $event->getSubject();
        /** @var \Cake\ORM\Query\SelectQuery $query */
        $query = $event->getData('query');
        $foreignKey = $action->getParentId();
        $field = $action->getParentIdName();
        if ($field !== null) {
            $query->where([$field => $foreignKey]);
        }
        if ($event->getResult()) {
            $query = $event->getResult();
        }

        return $query;
    }

    /**
     * On patch entity.
     *
     * @param \Cake\Event\Event $event An Event instance
     * @return \Cake\ORM\Entity
     */
    public function patchEntity(Event $event): \Cake\ORM\Entity
    {
        /** @var \CakeDC\Api\Service\Action\CrudAction $action */
        $action = $event->getSubject();

        /** @var \Cake\ORM\Entity $entity */
        $entity = $event->getData('entity');

        /** @var \Cake\ORM\Query\SelectQuery $query */
        $query = $event->getData('query');
        if ($event->getResult()) {
            $entity = $event->getResult();
        }
        $foreignKey = $action->getParentId();
        $field = $action->getParentIdName();

        if ($field !== null) {
            $entity->set($field, $foreignKey);
        }

        return $entity;
    }
}
