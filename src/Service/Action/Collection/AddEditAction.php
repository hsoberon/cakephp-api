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

/**
 * Class AddEditAction, uses POST and an array of entity data to either add or edit
 * Pass a not null primary key for edit, null for adding a new row
 * Validation done and using saveMany to store entities, atomic
 *
 * Example curl call
 *
 * curl --request POST --url http://example.com/api/posts/collection/add \
 *   --header 'content-type: multipart/form-data; boundary=---011000010111000001101001' \
 *   --form '0[id]=1' \
 *   --form '0[title]=Article1ForEdit' \
 *   --form '0[title]=NewArticle2'
 *
 * @package CakeDC\Api\Service\Action\Collection
 */
class AddEditAction extends CollectionAction
{
    /**
     * Apply validation process.
     *
     * @return bool
     */
    public function validates(): bool
    {
        return $this->_validateMany();
    }

    /**
     * Execute action.
     *
     * @return mixed
     */
    public function execute()
    {
        $keys = $this->getTable()->getPrimaryKey();
        $accessibleFields = [];
        if (is_array($keys)) {
            foreach ($keys as $key) {
                $accessibleFields[$key] = true;
            }
        } else {
            $accessibleFields = [$keys => true];
        }
        $entities = $this->_newEntities(['accessibleFields' => $accessibleFields]);

        return $this->_saveMany($entities);
    }
}
