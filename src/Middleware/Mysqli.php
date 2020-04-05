<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;

class Mysqli
{
  /**
   * @param Request                $request
   * @param string                 $attributeName
   *
   * @return MysqliMiddleware
   */
  public static function fromRequest(Request $request, string $attributeName = 'mysqli'): MysqliMiddleware
  {
    $mysqli = $request->getAttribute($attributeName);
    if ($mysqli === null || !($mysqli instanceof MysqliMiddleware)) {
      throw new \RuntimeException(
        'Twig could not be found in the server request attributes using the key "'. $attributeName .'".'
        );
    }
    
    return $mysqli;
  }
}

