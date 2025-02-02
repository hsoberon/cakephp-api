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

namespace CakeDC\Api\Test\TestCase\Service\Action;

use Cake\Datasource\Exception\RecordNotFoundException;
use CakeDC\Api\Service\Action\CrudDeleteAction;
use CakeDC\Api\Service\ServiceRegistry;
use CakeDC\Api\Test\ConfigTrait;
use CakeDC\Api\TestSuite\TestCase;

class CrudDeleteActionTest extends TestCase
{
    use ConfigTrait;

    public $request;

    public $response;

    public ?\CakeDC\Api\Service\Action\CrudDeleteAction $Action = null;

    protected ?\CakeDC\Api\Service\Service $Service = null;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        ServiceRegistry::getServiceLocator()->clear();
        unset($this->Service, $this->Action, $this->request);
        parent::tearDown();
    }

    /**
     * Test load value method
     *
     * @return void
     */
    public function testExecuteSuccess()
    {
        $this->_initializeAction(1, [
            'title' => 'New message',
        ]);

        $onFindEntity = false;
        $this->Action->getEventManager()->on('Action.Crud.onFindEntity', function () use (&$onFindEntity) {
            $onFindEntity = true;
        });

        $result = $this->Action->execute();
        $this->assertTrue($result);
        $this->assertTrue($onFindEntity);
    }

    /**
     * Test load value method
     *
     * @return void
     */
    public function testExecuteNotFound()
    {
        $this->expectException(RecordNotFoundException::class);
        $this->_initializeAction(999, [
            'title' => 'New message',
        ]);
        $this->Action->execute();
    }

    protected function _initializeAction($id, $post = [])
    {
        $this->_initializeRequest([
            'params' => [
                'service' => 'articles',
                'pass' => [
                    $id,
                ],
            ],
            'post' => $post,
        ], 'DELETE');
        $options = [
            'version' => null,
            'service' => $this->request->getParam('service'),
            'request' => $this->request,
            'response' => $this->response,
            'baseUrl' => '/articles/' . $id,
        ];
        $this->Service = ServiceRegistry::getServiceLocator()->get($this->request->getParam('service'), $options);

        $this->Action = new CrudDeleteAction([
            'service' => $this->Service,
            'id' => $id,
        ]);
    }
}
