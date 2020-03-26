<?php
declare(strict_types = 1);

use App\Controller\LoginController;
use App\Controller\ProductController;

$app->get('/product',
  ProductController::class.':listAction'
)->setName('product-list');

$app->get('/product/add',
  ProductController::class.':addFormAction'
)->setName('product-add-form');

$app->get('/product/{id}',
  ProductController::class.':viewAction'
)->setName('product-view');

$app->get('/product/{id}/update',
  ProductController::class.':updateFormAction'
)->setName('product-update-form');

$app->get('/login',
  LoginController::class.':loginFormAction'
)->setName('login-form');
