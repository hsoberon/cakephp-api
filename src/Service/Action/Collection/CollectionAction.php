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

namespace CakeDC\Api\Service\Action\Collection;

use Cake\Datasource\EntityInterface;
use Cake\Utility\Hash;
use CakeDC\Api\Exception\ValidationException;
use CakeDC\Api\Service\Action\CrudAction;

/**
 * Class CollectionAction
 *
 * @package CakeDC\Api\Service\Action\Collection
 */
abstract class CollectionAction extends CrudAction
{
    /**
     * Apply validation process to many entities
     *
     * @return bool
     */
    protected function _validateMany(): bool
    {
        $validator = $this->getTable()->getValidator();
        $data = $this->getData();
        $this->_validateDataIsArray($data);
        $index = 0;
        $errors = collection($data)->reduce(function ($errors, $data) use ($validator, &$index) {
            $error = $validator->validate($data);
            if ($error) {
                $errors[$index] = $error;
            }

            $index++;

            return $errors;
        }, []);

        if (!empty($errors)) {
            throw new ValidationException(__('Validation failed'), 0, null, $errors);
        }

        return true;
    }

    /**
     * Save many entities
     *
     * @param \Cake\Datasource\EntityInterface[]  $entities entities
     * @return \Cake\Datasource\EntityInterface[]|\Cake\Datasource\ResultSetInterface|array of entities saved
     * @throws \Exception
     */
    protected function _saveMany(iterable $entities)
    {
        if ($this->getTable()->saveMany($entities)) {
            return $entities;
        } else {
            $errors = collection($entities)->reduce(
                fn($errors, EntityInterface $entity) => array_merge($errors, $entity->getErrors()),
                []
            );
            $message = __('Validation on {0} failed', $this->getTable()->getAlias());
            throw new ValidationException($message, 0, null, $errors);
        }
    }

    /**
     * Create entities from the posted data
     *
     * @param array $patchOptions options to use in patch
     * @return \Cake\Datasource\EntityInterface[] entities
     */
    protected function _newEntities(array $patchOptions = [])
    {
        $data = $this->getData();
        $this->_validateDataIsArray($data);

        return collection($data)->reduce(function ($entities, $data) use ($patchOptions) {
            $entity = $this->_newEntity();
            $entity = $this->_patchEntity($entity, $data, $patchOptions);
            $entities[] = $entity;

            return $entities;
        }, []);
    }

    /**
     * Ensure the data is a not empty array
     *
     * @param mixed $data posted data
     * @throws \CakeDC\Api\Exception\ValidationException
     * @return void
     */
    protected function _validateDataIsArray($data): void
    {
        if (!is_array($data) || Hash::dimensions($data) < 2) {
            throw new ValidationException(__('Validation failed, POST data is not an array of items'), 0, null);
        }
    }
}
