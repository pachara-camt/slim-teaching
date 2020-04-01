<?php
declare(strict_types = 1);

use Slim\Routing\RouteCollectorProxy;

use App\Controller\CategoryController;
use App\Controller\LoginController;
use App\Controller\ProductController;
use App\Middleware\AuthorizationMiddleware;

$appGroup = $app->group('', function(RouteCollectorProxy $group) {
  $productGroup = $group->group('/product', function(RouteCollectorProxy $group) {
    $adminGroup = $group->group('', function(RouteCollectorProxy $group){
      $group->get('/add',
        ProductController::class.':addFormAction'
      )->setName('product-add-form');
      
      $group->post('/add',
        ProductController::class.':addAction'
      )->setName('product-add');
      
      $group->get('/{id}/update',
        ProductController::class.':updateFormAction'
      )->setName('product-update-form');
    
      $group->post('/{id}/update',
        ProductController::class.':updateAction'
      )->setName('product-update');
      
      $group->get('/{id}/delete',
        ProductController::class.':deleteAction'
      )->setName('product-delete');
    });
    
    $adminGroup->add(new AuthorizationMiddleware(
      $group->getResponseFactory(), ['ADMIN']
    ));
    
    $group->get('',
      ProductController::class.':listAction'
    )->setName('product-list');
    
    $group->get('/{id}',
      ProductController::class.':viewAction'
    )->setName('product-view');
  });

  $categoryGroup = $group->group('/category', function(RouteCollectorProxy $group) {
    $adminGroup = $group->group('', function(RouteCollectorProxy $group){
      $group->get('/add',
        CategoryController::class.':addFormAction'
      )->setName('category-add-form');
      
      $group->post('/add',
        CategoryController::class.':addAction'
      )->setName('category-add');
    });
    
    $group->get('',
      CategoryController::class.':listAction'
    )->setName('category-list');
  });
});

$appGroup->add(new AuthorizationMiddleware(
  $app->getResponseFactory(), ['USER', 'ADMIN']
));

$app->get('/login',
  LoginController::class.':loginFormAction'
)->setName('login-form');

$app->post('/login',
  LoginController::class.':loginAction'
)->setName('login');

$app->get('/logout',
  LoginController::class.':logoutAction'
)->setName('logout');
