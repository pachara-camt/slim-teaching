<?php
declare(strict_types = 1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Views\Twig;

class LoginController
{
  public function loginFormAction(
    Request $request, Response $response, $args
  ) : Response
  {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'login-form.html', [
      'data' => null,
    ]);
  }
}
