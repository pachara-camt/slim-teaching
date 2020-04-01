<?php
declare(strict_types = 1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class CategoryController
{
  public static function getAll($link) : array
  {
    $result = mysqli_query($link, sprintf(<<<EOT
SELECT * FROM category
ORDER BY name
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
SELECT * FROM category
WHERE id = '%s'
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
      $link = $request->getAttribute('mysqli')->connect();
      return $view->render($response, 'category-list.html', [
        'data' => self::getAll($link),
      ]);
  }

  public function addFormAction(
    Request $request, Response $response, $args
    ) : Response
    {
      $view = Twig::fromRequest($request);
      // Get connection from mysqli middleware
      $link = $request->getAttribute('mysqli')->connect();
      return $view->render($response, 'category-add-form.html', [
        'categoryList' => CategoryController::getAll($link),
      ]);
  }
  
  public function addAction(
    Request $request, Response $response, $args
  ) : Response
    {
      // we get post data from $request object instead of $_POST variable
      $post = $request->getParsedBody();
      $link = $request->getAttribute('mysqli')->connect();
      mysqli_query($link, sprintf(<<<EOT
INSERT INTO category (
  name
) VALUES (
  '%s'
)
EOT
      , mysqli_real_escape_string($link, $post['name'])
    ));
    
    // add successful message to flash session
    $request->getAttribute('session')
      ->getSegment(self::class)
      ->setFlash('message', "Adding is successful.");
    
    // redirect to category-list route with HTTP status code 302
    $routeContext = RouteContext::fromRequest($request);
    return $response->withHeader('Location',
      $routeContext->getRouteParser()->urlFor('category-list')
    )->withStatus(302);
  }
}
