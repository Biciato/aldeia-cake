<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 * Cache: Routes are cached to improve performance, check the RoutingMiddleware
 * constructor in your `src/Application.php` file to change this behavior.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    // Register scoped middleware for in scopes.
    $routes->registerMiddleware('csrf', new CsrfProtectionMiddleware([
        'httpOnly' => true
    ]));
    $routes->redirect(
        '/users/*',
        ['controller' => 'Login', 'action' => 'index'],
        ['persist' => true]
        // Or ['persist'=>['id']] for default routing where the
        // view action expects $id as an argument.
    );

    /**
     * Apply a middleware to the current route scope.
     * Requires middleware to be registered via `Application::routes()` with `registerMiddleware()`
     */
    $routes->applyMiddleware('csrf');


    $routes->connect('/', ['controller' => 'Dashboard', 'action' => 'index']);

    $routes->connect('/logout', ['controller' => 'Login', 'action' => 'index', true]);

    $routes->connect('/checar', ['controller' => 'Login', 'action' => 'checar', true]);


    $routes->connect('/:controller/lista', ['action' => 'index', true]);
    /*$routes->connect('/colaboradores/lista', ['controller' => 'Colaboradores', 'action' => 'index', true]);
    $routes->connect('/alunos/lista', ['controller' => 'Alunos', 'action' => 'index', true]);
    $routes->connect('/servicos/lista', ['controller' => 'servicos', 'action' => 'index', true]);*/

    $routes->connect('/agende-uma-visita', ['controller' => 'Prospects', 'action' => 'novo', true]);

    $routes->connect(
        '/documentos/ver/:tipo/:name/:id',
        ['controller' => 'documentos', 'action' => 'ver']
    )
    ->setPatterns(['id' => '\d+', 'name' => '.+', 'tipo' => '[a-z_]+'])
    ->setPass(['tipo', 'id']);

    $routes->connect(
        '/prospects/editar/:name/:id',
        ['controller' => 'prospects', 'action' => 'editar']
    )
    ->setPatterns(['id' => '\d+', 'name' => '.+'])
    ->setPass(['id']);

    $routes->connect(
        '/configurar/:aux',
        ['controller' => 'configuracao', 'action' => 'configurar']
    )
    ->setPatterns(['aux' => '.+'])
    ->setPass(['aux']);




    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *
     * ```
     * $routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);
     * $routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);
     * ```
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $routes->fallbacks(DashedRoute::class);
});

/**
 * If you need a different set of middleware or none at all,
 * open new scope and define routes there.
 *
 * ```
 * Router::scope('/api', function (RouteBuilder $routes) {
 *     // No $routes->applyMiddleware() here.
 *     // Connect API actions here.
 * });
 * ```
 */
$routes->scope('/api', function (RouteBuilder $builder) {
    $builder->setExtensions(['json']);
    $builder->registerMiddleware('bodyparser', \Cake\Http\Middleware\BodyParserMiddleware::class);
    $builder->registerMiddleware('checktoken', \App\Middleware\CheckTokenMiddleware::class);
    $builder->applyMiddleware('checktoken');

    $builder->post('/token', ['controller' => 'Token', 'action' => 'token']);
    $builder->post('/recuperar-senha', ['controller' => 'LoginAPI', 'action' => 'recuperarSenha']);
    $builder->fallbacks();
});
