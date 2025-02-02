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

namespace CakeDC\Api\Test\App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ArticlesTable extends Table
{
    /**
     * Initialize method
     *
     * @param  array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->setTable('articles');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Authors')->setForeignKey('author_id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', 'create');

        $validator
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        return $validator;
    }

    public function createRecords($count, $authorId, $templates = null)
    {
        if ($templates === null) {
            $templates = [
                'title' => 'Article N%s',
                'body' => 'Article N%s Body',
            ];
        }
        foreach (range(1, $count) as $itemId) {
            $article = $this->newEntity([]);
            $article['title'] = __($templates['title'], $itemId);
            $article['body'] = __($templates['body'], $itemId);
            $article['author_id'] = $authorId;
            $this->save($article);
        }
    }
}
