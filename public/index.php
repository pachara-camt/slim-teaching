<?php
declare(strict_types = 1);

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteInterface;

require_once __DIR__.'/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__."/..");
$env = $dotenv->load();

$app = AppFactory::create();
$app->setBasePath($_SERVER['BASE_PATH']);

// Load middlewares
$middlewaresConfig = require_once __DIR__.'/../config/middlewares.php';
$middlewaresConfig($app);

$app->addRoutingMiddleware();
$app->addErrorMiddleware(
  $_SERVER['APP_ENV'] == 'dev',
  true, true
);

// Load routes
$routesConfig = require_once __DIR__.'/../config/routes.php';
$routesConfig($app);

$app->run();
