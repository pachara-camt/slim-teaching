<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;

use Aura\Session\Session;

class AuraSession
{
  /**
   * @param Request                $request
   * @param string                 $attributeName
   *
   * @return Session
   */
  public static function fromRequest(Request $request, string $attributeName = 'session'): Session
  {
    $session = $request->getAttribute($attributeName);
    if ($session === null || !($session instanceof Session)) {
      throw new \RuntimeException(
        'AuraSession could not be found in the server request attributes using the key "'. $attributeName .'".'
        );
    }
    
    return $session;
  }
}
