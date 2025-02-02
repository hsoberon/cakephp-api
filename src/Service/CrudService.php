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

namespace CakeDC\Api\Service;

use Cake\Utility\Inflector;

/**
 * Class CrudService
 *
 * @package CakeDC\Api\Service
 */
abstract class CrudService extends Service
{
    /**
     * Actions classes map.
     *
     * @var array
     */
    protected array $_actionsClassMap = [
        'describe' => \CakeDC\Api\Service\Action\CrudDescribeAction::class,
        'index' => \CakeDC\Api\Service\Action\CrudIndexAction::class,
        'view' => \CakeDC\Api\Service\Action\CrudViewAction::class,
        'add' => \CakeDC\Api\Service\Action\CrudAddAction::class,
        'edit' => \CakeDC\Api\Service\Action\CrudEditAction::class,
        'delete' => \CakeDC\Api\Service\Action\CrudDeleteAction::class,
    ];

    /**
     * Table name.
     *
     * @var string
     */
    protected $_table = null;

    /**
     * Id param name.
     */
    protected string $_idName = 'id';

    /**
     * CrudService constructor.
     *
     * @param array $config Service configuration.
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        if (isset($config['table'])) {
            $this->setTable($config['table']);
        } else {
            $this->setTable(Inflector::camelize($this->getName()));
        }
    }

    /**
     * Gets a Table name.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->_table;
    }

    /**
     * Sets the table instance.
     *
     * @param string $table A Table name.
     * @return $this
     */
    public function setTable(string $table)
    {
        $this->_table = $table;

        return $this;
    }

    /**
     * Action constructor options.
     *
     * @param array $route Activated route.
     * @return array
     */
    protected function _actionOptions(array $route): array
    {
        $id = null;
        if (isset($route[$this->_idName])) {
            $id = $route[$this->_idName];
        }

        return parent::_actionOptions($route) + [
            'id' => $id,
            'idName' => $this->_idName,
        ];
    }
}
