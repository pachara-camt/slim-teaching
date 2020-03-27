<?php
declare(strict_types = 1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthorizationMiddleware implements MiddlewareInterface
{
  private $responseFactory;
  private $roles;
  
  public function __construct(
    ResponseFactory $responseFactory, $roles = null
  )
  {
    $this->responseFactory = $responseFactory;
    $this->roles = $roles;
  }

  // implement MiddleWareInterface
  public function process(
    Request $request, RequestHandler $handler
  ): Response
  {
    $response = null;
    $session = $request->getAttribute('session');
    $user = $session->getSegment('global')->get('user');
    if(empty($user) ||
        (($this->roles !== null) && !in_array($user['class'], $this->roles))) {
      $response = $this->responseFactory->createResponse(403);
    } else {
      // call other middlewares
      $response = $handler->handle($request);
    }
     
    return $response;
  }
}
