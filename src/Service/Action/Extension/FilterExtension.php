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
 * Class FilterExtension
 *
 * @package CakeDC\Api\Service\Action\Extension
 */
class FilterExtension extends Extension implements EventListenerInterface
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

        /** @var \Cake\ORM\Table $table */
        $table = $query->getRepository();
        $schema = $table->getSchema();
        $fields = $schema->columns();
        $fields = array_flip($fields);
        $data = $action->getData();
        $postfixDelimeter = '$';
        $filterPostfixes = [
            '' => '',
            'ge' => ' >=',
            'le' => ' <=',
            'gt' => ' >',
            'lt' => ' <',
            'llike' => ' LIKE',
            'rlike' => ' LIKE',
            'like' => ' LIKE',
            'ne' => ' !=',
        ];
        foreach ($filterPostfixes as $postfix => $rule) {
            $filter = collection($data)
                ->filter(function ($item, $key) use ($fields, $postfix, $postfixDelimeter) {
                    if ($postfix !== '') {
                        if (strpos($key, $postfixDelimeter . $postfix) === false) {
                            return false;
                        }
                        $key = str_replace($postfixDelimeter . $postfix, '', $key);
                    }

                    return array_key_exists($key, $fields);
                })
                ->toArray();

            if (!empty($filter)) {
                foreach ($filter as $field => $value) {
                    if ($postfix == 'ge' || $postfix == 'ne') {
                        unset($data[$field]);
                    }
                    if (is_array($value)) {
                        if ($postfix == '') {
                            $query->where([$field . ' IN' => $value]);
                        } elseif ($postfix == 'ne') {
                            $query->where([$field . ' NOT IN' => $value]);
                        }
                    } else {
                        if ($postfix !== '') {
                            $field = str_replace($postfixDelimeter . $postfix, '', $field) . $rule;
                            if ($postfix == 'llike' || $postfix == 'like') {
                                $value = '%' . $value;
                            }
                            if ($postfix == 'rlike' || $postfix == 'like') {
                                $value .= '%';
                            }
                        }
                        $query->where([$field => $value]);
                    }
                }
            }
        }

        return $query;
    }
}
