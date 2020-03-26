<?php
declare(strict_types = 1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Views\Twig;

class ProductController
{
  // This is just a mock up data, the real data come from database
  private static $PRODUCT_DATA = [
    ['id' => 101, 'name' => 'Product A', 'category_name' => 'Phone',  'price' => 1234.56, 'qty' => 100 ],
    ['id' => 250, 'name' => 'Product B', 'category_name' => 'Phone',  'price' => 2345.67, 'qty' => 200 ],
    ['id' => 400, 'name' => 'Product C', 'category_name' => 'Tablet', 'price' => 3456.78, 'qty' => 300 ],
  ];
  
  public function listAction(
    Request $request, Response $response, $args
  ) : Response
  {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'product-list.html', [
      'data' => self::$PRODUCT_DATA,
    ]);
  }

  public function addFormAction(
    Request $request, Response $response, $args
  ) : Response
  {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'product-add-form.html', [
      'data' => null,
    ]);
  }
  
  public function viewAction(
    Request $request, Response $response, $args
  ) : Response
  {
    // $args store value from placeholder that we want {id}
    $id = $args['id'];
    $item = null;
    foreach(self::$PRODUCT_DATA as $product)
      if($product['id'] == $id) $item = $product;
    $view = Twig::fromRequest($request);
    return $view->render($response, 'product-view.html', [
      'data' => $item,
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
