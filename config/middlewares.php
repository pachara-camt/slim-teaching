<?php
declare(strict_types = 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Slim\App;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

use App\Middleware\MysqliMiddleware;
use App\Middleware\AuraSessionMiddleware;
use App\Middleware\AuraSession;

return function(App $app) {
  // add session to template, we start variable name
  // with _ to prevent name collision.
  $app->add(function(
    Request $request, RequestHandler $handler
  ) : Response {
    $view = Twig::fromRequest($request);
    
    // get session object from middleware
    $session = AuraSession::fromRequest($request);
    
    // add global session with name _global
    $view->offsetSet('_global', $session->getSegment('global'));
    
    // add segment session that is related with controller
    // with name _session
    $routeContext = RouteContext::fromRequest($request);
    $callable = $routeContext->getRoute()->getCallable();
    if(is_string($callable)) {
      $className = explode(':', $callable, 2)[0];
      $view->offsetSet('_session', $session->getSegment($className));
    }
    
    return $handler->handle($request);
  });
  
  // Create Twig.
  // We don't want cache on development environment.
  $twig = Twig::create(__DIR__.'/../templates',
    ($_SERVER['APP_ENV'] == 'dev')?
    [] : ['cache' => __DIR__.'/../cache']
  );
  
  // Add Twig-View Middleware.
  $app->add(TwigMiddleware::create($app, $twig));
  
  // Add mysqli Middleware with parameters from .env file.
  $app->add(new MysqliMiddleware([
    'default' => [
      'host' => $_SERVER['DB_HOST'],
      'username' => $_SERVER['DB_USERNAME'],
      'password' => $_SERVER['DB_PASSWORD'],
      'dbname' => $_SERVER['DB_DBNAME'],
    ],
  ]));
  
  $session = new AuraSessionMiddleware();
  
  // Assign cookie name to isolate our session from other applications,
  // change 612110xxx to your student ID
  $session->name('612110xxx_SLIM');
  
  // Add Aura.Session middleware
  $app->add($session);
};
