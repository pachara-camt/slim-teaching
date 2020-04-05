<?php
declare(strict_types = 1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class LoginController
{
  public function loginFormAction(
    Request $request, Response $response, $args
  ) : Response
  {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'login/form.html', [
      'data' => null,
    ]);
  }

  public function loginAction(
    Request $request, Response $response, $args
  ) : Response
  {
    $post = $request->getParsedBody();
    $link = $request->getAttribute('mysqli')->connect();
    $result = mysqli_query($link, sprintf(<<<EOT
SELECT * FROM systemuser WHERE username = '%s'
LIMIT 0, 1;
EOT
      , mysqli_real_escape_string($link, $post['username'])
    ));
    
    $user = mysqli_fetch_assoc($result);
    $routeContext = RouteContext::fromRequest($request);
    $session = $request->getAttribute('session');

    // in the case of login success we assign user data to global session with key user
    // and then redirect to product-list route
    // otherwise redirect to login-form route with flash error message
    if(!empty($user) && ($user['passwd'] === $post['passwd'])) {
      $globalSegment = $session->getSegment('global');
      $globalSegment->set('user', $user);
      $response = $response->withHeader('Location',
        $routeContext->getRouteParser()->urlFor('product-list')
        )->withStatus(302);
    } else {
      $segment = $session->getSegment(self::class);
      $segment->setFlash('error', 'Login Fail!!!');
      $response = $response->withHeader('Location',
        $routeContext->getRouteParser()->urlFor('login-form')
        )->withStatus(302);
    }
    
    return $response;
  }

  public function logoutAction(
    Request $request, Response $response, $args
  ) : Response
  {
    $session = $request->getAttribute('session');
    $globalSegment = $session->getSegment('global');
    $globalSegment->clear();
    $routeContext = RouteContext::fromRequest($request);
    return $response->withHeader('Location',
      $routeContext->getRouteParser()->urlFor('login-form')
    )->withStatus(302);
  }
}
