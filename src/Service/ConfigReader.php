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

use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

class ConfigReader
{
    /**
     * Builds service options.
     *
     * @param string $serviceName Service name.
     * @param ?string $version Version number.
     * @return array
     */
    public function serviceOptions(string $serviceName, ?string $version = null): array
    {
        $defaults = $this->_checkServiceOptions('default.options');
        if (Configure::read('Api.useVersioning') && $version) {
            $versionDefaults = $this->_checkServiceOptions("$version.default.options");
            $options = $this->_checkServiceOptions("$version.$serviceName.options");
            $options = $this->_mergeWithDefaults($options, $versionDefaults, true);
        } else {
            $options = $this->_checkServiceOptions("$serviceName.options");
        }

        return $this->_mergeWithDefaults($options, $defaults, true);
    }

    /**
     * Builds action options.
     *
     * @param string $serviceName A Service name.
     * @param string $actionName An Action name.
     * @param ?string $version Version number.
     * @return array
     */
    public function actionOptions(string $serviceName, string $actionName, ?string $version = null): array
    {
        $actionName = Inflector::camelize($actionName);
        $defaults = $this->_checkServiceOptions('default.Action.default');
        $defaultByName = $this->_checkServiceOptions("default.Action.$actionName");
        if (Configure::read('Api.useVersioning') && $version) {
            $versionDefaults = $this->_checkServiceOptions("$version.default.Action.default");
            $versionDefaultsByName = $this->_checkServiceOptions("$version.default.Action.$actionName");

            $byServiceDefault = $this->_checkServiceOptions("$version.$serviceName.Action.default");
            $byServiceOptions = $this->_checkServiceOptions("$version.$serviceName.Action.$actionName");

            $options = $this->_mergeWithDefaults($byServiceOptions, $byServiceDefault);
            $options = $this->_mergeWithDefaults($options, $versionDefaultsByName);
            $options = $this->_mergeWithDefaults($options, $versionDefaults);
        } else {
            $byServiceDefault = $this->_checkServiceOptions("$serviceName.Action.default");
            $byServiceOptions = $this->_checkServiceOptions("$serviceName.Action.$actionName");

            $options = $this->_mergeWithDefaults($byServiceOptions, $byServiceDefault);
        }

        $options = $this->_mergeWithDefaults($defaultByName, $options);

        return $this->_mergeWithDefaults($defaults, $options);
    }

    /**
     * Check options existence by prefix.
     *
     * @param string $prefix Path prefix.
     * @return \ArrayAccess|array
     */
    protected function _checkServiceOptions(string $prefix)
    {
        $data = Configure::read('Api.Service');
        if (is_array($data) && Hash::check($data, $prefix)) {
            return Hash::extract($data, $prefix);
        }

        return [];
    }

    /**
     * Merge with defaults
     *
     * @param array $options An options.
     * @param array $defaults Default options.
     * @param bool $overwrite Overwrite flag.
     * @return array
     */
    protected function _mergeWithDefaults(array $options, array $defaults, bool $overwrite = false): array
    {
        if ($overwrite) {
            foreach ($options as $key => $value) {
                if ($value !== null) {
                    unset($defaults[$key]);
                }
            }
        }

        return Hash::merge($options, $defaults);
    }
}
