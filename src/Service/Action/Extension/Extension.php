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

use Cake\Core\InstanceConfigTrait;
use CakeDC\Api\Service\Action\ExtensionRegistry;

/**
 * Class Extension
 *
 * @package CakeDC\Api\Service\Action\Extension
 */
abstract class Extension
{
    use InstanceConfigTrait;

    protected array $_defaultConfig = [];

    /**
     * ExtensionRegistry instance.
     */
    protected \CakeDC\Api\Service\Action\ExtensionRegistry $_registry;

    /**
     * Extension constructor.
     *
     * @param \CakeDC\Api\Service\Action\ExtensionRegistry $registry An ExtensionRegistry instance.
     * @param array $config Configuration.
     */
    public function __construct(ExtensionRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $this->setConfig($config);
    }

    /**
     * Method which used to define if extension need to be used directly from Action class.
     * By default is extensions is not attachable.
     *
     * @return bool
     */
    public function attachable(): bool
    {
        return $this->getConfig('attachable') === true;
    }
}
