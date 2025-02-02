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
use Cake\ORM\Query\SelectQuery;
use CakeDC\Api\Service\Action\CrudAction;

/**
 * Class CrudAutocompleteListExtension
 *
 * @package CakeDC\Api\Service\Action\Extension
 */
class CrudAutocompleteListExtension extends Extension implements EventListenerInterface
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
        ];
    }

    /**
     * On find entities.
     *
     * @param \Cake\Event\Event $event An Event instance.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findEntities(Event $event): SelectQuery
    {
        /** @var \CakeDC\Api\Service\Action\CrudAction $action */
        $action = $event->getSubject();
        /** @var \Cake\ORM\Query\SelectQuery $query */
        $query = $event->getData('query');

        return $this->_autocompleteList($action, $query);
    }

    /**
     * @param \CakeDC\Api\Service\Action\CrudAction $action An Action instance.
     * @param \Cake\ORM\Query\SelectQuery $query A Query instance.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function _autocompleteList(CrudAction $action, SelectQuery $query): SelectQuery
    {
        $data = $action->getData();
        if (!(is_array($data) && !empty($data['autocomplete_list']))) {
            return $query;
        }

        return $query->select([
            $action->getTable()->getPrimaryKey(),
            $action->getTable()->getDisplayField(),
        ]);
    }
}
