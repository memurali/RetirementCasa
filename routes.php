<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
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
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/*
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
 */
/** @var \Cake\Routing\RouteBuilder $routes */
$routes->setRouteClass(DashedRoute::class);

$routes->scope('/', function (RouteBuilder $builder) {
    // Register scoped middleware for in scopes.
    $builder->registerMiddleware('csrf', new CsrfProtectionMiddleware([
        'httpOnly' => true,
    ]));

    /*
     * Apply a middleware to the current route scope.
     * Requires middleware to be registered through `Application::routes()` with `registerMiddleware()`
     */
    $builder->applyMiddleware('csrf');

    /*
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, templates/Pages/home.php)...
     */
	 
	//$builder->connect('/:action', ['controller' => 'Users']);
    $builder->connect('/', ['controller' => 'Users', 'action' => 'index']);
    
    $builder->connect('/:param1',
		array('controller' => 'users', 'action' => 'contact'),
		array(
		    'param1' => 'contact',
			'pass' => array('param1'),
		)
	);
	
	$builder->connect('/:param1',
		array('controller' => 'users', 'action' => 'about'),
		array(
		    'param1' => 'about',
			'pass' => array('param1'),
		)
	);
	
	$builder->connect('/:param1',
		array('controller' => 'users', 'action' => 'index'),
		array(
			'pass' => array('param1'),
		)
	);
	
	$builder->connect('/:param/:param1',
		array('controller' => 'users', 'action' => 'signin'),
		array(
			'param' => 'users',
			'param1' => 'signin',
			'pass' => array('param','param1'),
		)
	);
	
	$builder->connect('/:param/:param1',
		array('controller' => 'users', 'action' => 'signout'),
		array(
			'param' => 'users',
			'param1' => 'signout',
			'pass' => array('param','param1'),
		)
	);
	
	$builder->connect('/:param/:param1',
		array('controller' => 'users', 'action' => 'admin-dashboard'),
		array(
			'param' => 'users',
			'param1' => 'admin-dashboard|admin_dashboard|adminDashboard',
			'pass' => array('param','param1'),
		)
	);
	
	$builder->connect('/:param/:param1',
		array('controller' => 'users', 'action' => 'admin-dashboard-live'),
		array(
			'param' => 'users',
			'param1' => 'admin-dashboard-live|admin_dashboard_live|adminDashboardLive',
			'pass' => array('param','param1'),
		)
	);
	
	$builder->connect('/:param/:param1',
		array('controller' => 'users', 'action' => 'admin-dashboard-users'),
		array(
			'param' => 'users',
			'param1' => 'admin-dashboard-users|admin_dashboard_users|adminDashboardUsers',
			'pass' => array('param','param1'),
		)
	);
	
	$builder->connect('/:param/:param1',
		array('controller' => 'users', 'action' => 'admin-dashboard-staging'),
		array(
			'param' => 'users',
			'param1' => 'admin-dashboard-staging|admin_dashboard_staging|adminDashboardStaging',
			'pass' => array('param','param1'),
		)
	);
	
	$builder->connect('/:param/:param1',
		array('controller' => 'users', 'action' => 'admin-setting'),
		array(
			'param' => 'users',
			'param1' => 'admin-setting|admin_setting|adminSetting',
			'pass' => array('param','param1'),
		)
	);
	
	$builder->connect('/:param/:param1',
		array('controller' => 'users', 'action' => 'user-dashboard'),
		array(
			'param' => 'users',
			'param1' => 'user-dashboard|user_dashboard|userDashboard',
			'pass' => array('param','param1'),
		)
	);
	
	$builder->connect('/:param/:param1',
		array('controller' => 'users', 'action' => 'user-dashboard-saved'),
		array(
			'param' => 'users',
			'param1' => 'user-dashboard-saved|user_dashboard_saved|userDashboardSaved',
			'pass' => array('param','param1'),
		)
	);
	
	$builder->connect('/:param/:param1',
		array('controller' => 'users', 'action' => 'signup'),
		array(
			'param' => 'users',
			'param1' => 'signup',
			'pass' => array('param','param1'),
		)
	);
	
	$builder->connect('/:param/:param1',
		array('controller' => 'users', 'action' => 'dbchange'),
		array(
			'param' => 'users',
			'param1' => 'dbchange',
			'pass' => array('param','param1'),
		)
	);
	
	$builder->connect('/:param/:param1',
		array('controller' => 'users', 'action' => 'not-found'),
		array(
			'param' => 'users',
			'param1' => 'not-found|not_found|notFound',
			'pass' => array('param','param1'),
		)
	);
	
	$builder->connect('/:param/:param1',
		array('controller' => 'users', 'action' => 'index'),
		array(
			'pass' => array('param','param1'),
		)
	);
	
	
	
    /*
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    $builder->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

    /*
     * Connect catchall routes for all controllers.
     *
     * The `fallbacks` method is a shortcut for
     *
     * ```
     * $builder->connect('/:controller', ['action' => 'index']);
     * $builder->connect('/:controller/:action/*', []);
     * ```
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $builder->fallbacks();
});

/*
 * If you need a different set of middleware or none at all,
 * open new scope and define routes there.
 *
 * ```
 * $routes->scope('/api', function (RouteBuilder $builder) {
 *     // No $builder->applyMiddleware() here.
 *     // Connect API actions here.
 * });
 * ```
 */