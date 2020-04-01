<?php
declare(strict_types = 1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

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
}


