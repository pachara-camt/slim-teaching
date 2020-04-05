<?php
declare(strict_types = 1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;

use App\Middleware\AuraSession;
use App\Middleware\Mysqli;

class ProductController
{
  public static function getAll($link) : array
  {
    $result = mysqli_query($link, sprintf(<<<EOT
SELECT product.*, category.name AS category_name FROM product
  LEFT JOIN category ON (product.id_category = category.id)
ORDER BY product.name
EOT
    ));
    
    $items = [];
    while($item = mysqli_fetch_assoc($result)) {
      $items[] = $item;
    }
    
    return $items;
  }
  
  public static function getItem($link, $id) : ?array
  {
    $result = mysqli_query($link, sprintf(<<<EOT
SELECT product.*, category.name AS category_name FROM product
  LEFT JOIN category ON (product.id_category = category.id)
WHERE product.id = '%s'
EOT
      , mysqli_real_escape_string($link, (string)$id)
    ));
    
    return mysqli_fetch_assoc($result);
  }
  
  public function listAction(
    Request $request, Response $response, $args
  ) : Response
  {
    $view = Twig::fromRequest($request);
    // Get connection from mysqli middleware
    $link = Mysqli::fromRequest($request)->connect();
    return $view->render($response, 'product/list.html', [
      'data' => self::getAll($link),
    ]);
  }

  public function addFormAction(
    Request $request, Response $response, $args
  ) : Response
  {
    $view = Twig::fromRequest($request);
    // Get connection from mysqli middleware
    $link = Mysqli::fromRequest($request)->connect();
    return $view->render($response, 'product/form.html', [
      'data' => null,
      'categoryList' => CategoryController::getAll($link),
    ]);
  }
  
  public function addAction(
    Request $request, Response $response, $args
  ) : Response
  {
    // we get post data from $request object instead of $_POST variable
    $post = $request->getParsedBody();
    $link = Mysqli::fromRequest($request)->connect();
    mysqli_query($link, sprintf(<<<EOT
INSERT INTO product (
  id_category, name, price, qty
) VALUES (
  '%s',        '%s', '%s',  '%s'
)
EOT
      , mysqli_real_escape_string($link, $post['id_category'])
      , mysqli_real_escape_string($link, $post['name'])
      , mysqli_real_escape_string($link, $post['price'])
      , mysqli_real_escape_string($link, $post['qty'])
    ));
    
    // add successful message to flash session
    AuraSession::fromRequest($request)
      ->getSegment(self::class)
      ->setFlash('message', "Adding {$post['name']} is successful.");
    
    // redirect to product-list route with HTTP status code 302
    $routeContext = RouteContext::fromRequest($request);
    return $response->withHeader('Location',
      $routeContext->getRouteParser()->urlFor('product-list')
    )->withStatus(302);
  }

  public function viewAction(
    Request $request, Response $response, $args
  ) : Response
  {
    $view = Twig::fromRequest($request);
    // Get connection from mysqli middleware
    $link = Mysqli::fromRequest($request)->connect();
    return $view->render($response, 'product/view.html', [
      'data' => self::getItem($link, $args['id']),
    ]);
  }

  public function updateFormAction(
    Request $request, Response $response, $args
  ) : Response
  {
    $view = Twig::fromRequest($request);
    // Get connection from mysqli middleware
    $link = Mysqli::fromRequest($request)->connect();
    return $view->render($response, 'product/form.html', [
      'data' => self::getItem($link, $args['id']),
      'categoryList' => CategoryController::getAll($link),
    ]);
  }

  public function updateAction(
    Request $request, Response $response, $args
  ) : Response
    {
      // we get post data from $request object instead of $_POST variable
      $post = $request->getParsedBody();
      $link = Mysqli::fromRequest($request)->connect();
      mysqli_query($link, sprintf(<<<EOT
UPDATE product SET
  id_category = '%s',
  name        = '%s',
  price       = '%s',
  qty         = '%s'
WHERE
  id = '%s'
EOT
        , mysqli_real_escape_string($link, $post['id_category'])
        , mysqli_real_escape_string($link, $post['name'])
        , mysqli_real_escape_string($link, $post['price'])
        , mysqli_real_escape_string($link, $post['qty'])
        , mysqli_real_escape_string($link, $args['id'])
      ));
      
      // add successful message to flash session
      AuraSession::fromRequest($request)
        ->getSegment(self::class)
        ->setFlash('message', "Updating {$post['name']} is successful.");
      
      // redirect to product-view route with HTTP status code 302
      $routeContext = RouteContext::fromRequest($request);
      return $response->withHeader('Location',
        $routeContext->getRouteParser()->urlFor('product-view', ['id' => $args['id']])
      )->withStatus(302);
  }

  public function deleteAction(
    Request $request, Response $response, $args
  ) : Response
    {
      $link = Mysqli::fromRequest($request)->connect();
      $item = self::getItem($link, $args['id']);
      $routeContext = RouteContext::fromRequest($request);
      $targetUrl = null;
      if(!empty($item)) {
        mysqli_query($link, sprintf(<<<EOT
DELETE FROM product WHERE id = '%s'
EOT
          , mysqli_real_escape_string($link, $item['id'])
        ));
        
        // add successful message to flash session
        AuraSession::fromRequest($request)
          ->getSegment(self::class)
          ->setFlash('message', "Deleting {$item['name']} is successful.");
        
        $targetUrl = $routeContext->getRouteParser()->urlFor('product-list');
      } else {
        // add successful message to flash session
        AuraSession::fromRequest($request)
          ->getSegment(self::class)
          ->setFlash('error', "Deleted target (id = {$args['id']}) is not found.");
        
        $targetUrl = $routeContext->getRouteParser()->urlFor('product-view');
      }
      
      // redirect to product-list route with HTTP status code 302
      return $response->withHeader('Location', $targetUrl)->withStatus(302);
  }
}
