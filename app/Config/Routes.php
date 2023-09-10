<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/Product', 'Product::index');
$routes->get('/Product/show', 'Product::show');
$routes->post('/Product/new', 'Product::new');
$routes->put('/Product/update', 'Product::update');
$routes->put('/Product/delete', 'Product::delete');
$routes->resource('Product');
