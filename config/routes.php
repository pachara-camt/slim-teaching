<?php
declare(strict_types = 1);

use Slim\Routing\RouteCollectorProxy;

use App\Controller\LoginController;
use App\Controller\ProductController;

$productGroup = $app->group('/product', function(RouteCollectorProxy $group) {
  $group->get('',
    ProductController::class.':listAction'
  )->setName('product-list');
  
  $group->get('/add',
    ProductController::class.':addFormAction'
  )->setName('product-add-form');
  
  $group->post('/add',
    ProductController::class.':addAction'
  )->setName('product-add');
  
  $group->get('/{id}',
    ProductController::class.':viewAction'
  )->setName('product-view');
  
  $group->get('/{id}/update',
    ProductController::class.':updateFormAction'
  )->setName('product-update-form');
});

$app->get('/login',
  LoginController::class.':loginFormAction'
)->setName('login-form');

$app->post('/login',
  LoginController::class.':loginAction'
)->setName('login');

$app->get('/logout',
  LoginController::class.':logoutAction'
)->setName('logout');
