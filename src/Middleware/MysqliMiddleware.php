<?php
declare(strict_types = 1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class MysqliMiddleware implements MiddlewareInterface
{
  private $configs;
  
  private $attributeName;
  
  public function __construct(array $configs, string $attributeName = 'mysqli')
  {
    $this->configs = $configs;
    $this->attributeName = $attributeName;
    
    // force mysqli throw exception when gets errors.
    mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX);
  }
  
  public function getAttributeName() : string
  {
    return $this->attributeName;
  }
  
  public function setAttributeName(string $attributeName) : void
  {
    $this->attributeName = $attributeName;
  }
  
  // implement MiddleWareInterface
  public function process(
    Request $request, RequestHandler $handler
  ): Response
  {
    $mysqli = new Mysqli($this->configs);
    
    // store connection object to attribute mysqli
    $request = $request->withAttribute($this->attributeName, $mysqli);
    
    // let other middlewares perform their task
    $response = $handler->handle($request);
    
    // close connection when other middlewares finish
    $mysqli->close();
    
    return $response;
  }
}

