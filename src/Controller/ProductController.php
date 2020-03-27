<?php
declare(strict_types = 1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class ProductController
{
  // This is just a mock up data, the real data come from database
  private static $PRODUCT_DATA = [
    ['id' => 101, 'name' => 'Product A', 'category_name' => 'Phone',  'price' => 1234.56, 'qty' => 100 ],
    ['id' => 250, 'name' => 'Product B', 'category_name' => 'Phone',  'price' => 2345.67, 'qty' => 200 ],
    ['id' => 400, 'name' => 'Product C', 'category_name' => 'Tablet', 'price' => 3456.78, 'qty' => 300 ],
  ];
  
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
    $link = $request->getAttribute('mysqli')->connect();
    return $view->render($response, 'product-list.html', [
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
    return $view->render($response, 'product-add-form.html', [
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
    $request->getAttribute('session')
      ->getSegment(self::class)
      ->setFlash('message', "Adding is successful.");
    
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
    $link = $request->getAttribute('mysqli')->connect();
    return $view->render($response, 'product-view.html', [
      'data' => self::getItem($link, $args['id']),
    ]);
  }

  public function updateFormAction(
    Request $request, Response $response, $args
  ) : Response
  {
    // $args store value from placeholder that we want {id}
    $id = $args['id'];
    $item = null;
    foreach(self::$PRODUCT_DATA as $product)
      if($product['id'] == $id) $item = $product;
    $view = Twig::fromRequest($request);
    return $view->render($response, 'product-update-form.html', [
      'data' => $item,
    ]);
  }
}
