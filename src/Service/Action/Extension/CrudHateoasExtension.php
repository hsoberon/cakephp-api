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
use Cake\Utility\Inflector;
use CakeDC\Api\Service\Action\Action;
use CakeDC\Api\Service\Action\CrudAction;
use CakeDC\Api\Service\Action\ExtensionRegistry;
use CakeDC\Api\Service\Utility\ReverseRouting;

/**
 * Class CrudHateoasExtension
 *
 * @package CakeDC\Api\Service\Action\Extension
 */
class CrudHateoasExtension extends Extension implements EventListenerInterface
{
    protected \CakeDC\Api\Service\Utility\ReverseRouting $_reverseRouter;

    /**
     * CrudHateous Extension constructor.
     *
     * @param \CakeDC\Api\Service\Action\ExtensionRegistry $registry An ExtensionRegistry instance.
     * @param array $config Configuration.
     */
    public function __construct(ExtensionRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
        $this->_reverseRouter = new ReverseRouting();
    }

    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Action.afterProcess' => 'afterAction',
        ];
    }

    /**
     * After action callback.
     *
     * @param \Cake\Event\Event $event An Event instance.
     * @return void
     */
    public function afterAction(Event $event): void
    {
        /** @var \CakeDC\Api\Service\Action\Action $action */
        $action = $event->getSubject();
        $result = $action->getService()->getResult();
        $actionName = $action->getName();
        $links = [];
        //$route = $action->route();
        if ($actionName == 'view') {
            $links = $this->_buildViewLinks($action);
        }
        if ($actionName == 'index') {
            $links = $this->_buildIndexLinks($action);
        }

        $parent = $action->getService()->getParentService();

        if ($parent !== null) {
            $result = $parent->getResult();
        }
        $result->appendPayload('links', $links);
    }

    /**
     * Builds index action links.
     *
     * @param \CakeDC\Api\Service\Action\Action $action An Action instance.
     * @return array
     */
    protected function _buildIndexLinks(Action $action): array
    {
        $links = [];
        $indexRoute = $action->getRoute();
        $version = $action->getService()->getVersion();
        $parent = $action->getService()->getParentService();
        $path = $this->_reverseRouter->indexPath($action);

        $links[] = $this->_reverseRouter->link('self', $path, $indexRoute['_method'], $version);
        $links[] = $this->_reverseRouter->link($action->getService()->getName() . ':add', $path, 'POST', $version);

        if ($parent !== null) {
            $parentName = $parent->getName() . ':view';
            $path = $this->_reverseRouter->parentViewPath($parentName, $action, 'view');
            $links[] = $this->_reverseRouter->link($parentName, $path, 'GET', $version);
        }

        return $links;
    }

    /**
     * Builds view action links.
     *
     * @param \CakeDC\Api\Service\Action\Action $action An Action instance.
     * @return array
     */
    protected function _buildViewLinks(Action $action): array
    {
        $links = [];
        $viewRoute = $action->getRoute();
        $service = $action->getService();
        $version = $service->getVersion();
        $parent = $action->getService()->getParentService();
        $path = null;
        if ($parent !== null) {
            $parentRoutes = $parent->routes();
            $currentRoute = $this->_reverseRouter->findRoute($viewRoute, $parentRoutes);
            if ($currentRoute !== null) {
                $path = $parent->routeReverse($viewRoute);
                array_pop($viewRoute['pass']);

                $indexName = $service->getName() . ':index';
                $indexPath = $this->_reverseRouter->parentViewPath($indexName, $action, 'index');
            }
        } else {
            $path = $service->routeReverse($viewRoute);
            array_pop($viewRoute['pass']);

            $indexName = $service->getName() . ':index';
            $route = collection($service->routes())
                ->filter(fn($item) => $item->getName() == $indexName)
                ->first();
            $indexPath = $service->routeReverse($route->defaults);
        }

        $links[] = $this->_reverseRouter->link('self', $path, $viewRoute['_method'], $version);
        $links[] = $this->_reverseRouter->link($action->getService()->getName() . ':edit', $path, 'PUT', $version);
        $links[] = $this->_reverseRouter->link($action->getService()->getName() . ':delete', $path, 'DELETE', $version);
        if (!empty($indexPath)) {
            $routeName = $action->getService()->getName() . ':index';
            $links[] = $this->_reverseRouter->link($routeName, $indexPath, 'GET', $version);
        }

        if ($parent === null && $action instanceof CrudAction) {
            $table = $action->getTable();
            $hasMany = $table->associations()->getByType('HasMany');
            foreach ($hasMany as $assoc) {
                $target = $assoc->getTarget();
                $alias = $target->getAlias();

                $targetClass = get_class($target);
                [, $className] = namespaceSplit($targetClass);
                $className = preg_replace('/(.*)Table$/', '\1', $className);
                if ($className === '') {
                    $className = $alias;
                }
                $serviceName = strtolower($className);

                $indexName = $serviceName . ':index';
                $route = collection($service->routes())
                    ->filter(fn($item) => $item->getName() == $indexName)
                    ->first();

                $currentId = Inflector::singularize(Inflector::underscore($service->getName())) . '_id';
                $defaults = empty($route->defaults) ? [] : $route->defaults;

                if (isset($route)) {
                    $viewRoute = $action->getRoute();
                    $defaults[$currentId] = $viewRoute['id'];
                    $indexPath = $service->routeReverse($defaults);

                    $links[] = $this->_reverseRouter->link($serviceName . ':index', $indexPath, 'GET', $version);
                }
            }
        }

        if ($parent !== null) {
            $parentName = $parent->getName() . ':view';
            $path = $this->_reverseRouter->parentViewPath($parentName, $action, 'view');
            $links[] = $this->_reverseRouter->link($parentName, $path, 'GET', $version);
        }

        return $links;
    }
}
