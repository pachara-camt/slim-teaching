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
      $group->get('',
        CategoryController::class.':listAction'
      )->setName('category-list');
      
      $group->get('/add',
        CategoryController::class.':addFormAction'
      )->setName('category-add-form');
      
      $group->post('/add',
        CategoryController::class.':addAction'
      )->setName('category-add');
      
      $group->get('/{id}',
        CategoryController::class.':viewAction'
      )->setName('category-view');
      
      $group->get('/{id}/update',
        CategoryController::class.':updateFormAction'
      )->setName('category-update-form');
      
      $group->post('/{id}/update',
        CategoryController::class.':updateAction'
      )->setName('category-update');
      
      $group->get('/{id}/delete',
        CategoryController::class.':deleteAction'
      )->setName('category-delete');
    });
    
    $adminGroup->add(new AuthorizationMiddleware(
      $group->getResponseFactory(), ['ADMIN']
    ));
  });
});

$appGroup->add(new AuthorizationMiddleware(
  $app->getResponseFactory(), ['USER', 'ADMIN']
));

$loginGroup = $app->group('', function(RouteCollectorProxy $group) {
  $group->get('/login',
    LoginController::class.':loginFormAction'
  )->setName('login-form');
  
  $group->post('/login',
    LoginController::class.':loginAction'
  )->setName('login');
  
  $group->get('/logout',
    LoginController::class.':logoutAction'
  )->setName('logout');
});
