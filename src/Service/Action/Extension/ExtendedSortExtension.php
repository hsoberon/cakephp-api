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

class ExtendedSortExtension extends Extension implements EventListenerInterface
{
    /**
     * Default settings
     *
     * @var array
     */
    protected array $_defaultConfig = [
        'sortField' => 'sort',
    ];

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
     * find entities
     *
     * @param \Cake\Event\Event $event An Event instance
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findEntities(Event $event): \Cake\ORM\Query\SelectQuery
    {
        $action = $event->getSubject();
        $query = $event->getData('query');
        if ($event->getResult()) {
            $query = $event->getResult();
        }
        $data = $action->getData();
        $sort = null;

        $sortField = $this->getConfig('sortField');
        if (!empty($data[$sortField])) {
            $sort = json_decode($data[$sortField], true, 512, JSON_THROW_ON_ERROR);
        }
        if (is_array($sort)) {
            $query->order($sort);
        }

        return $query;
    }
}
