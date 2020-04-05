<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;

class Mysqli
{
  /**
   * @param Request                $request
   * @param string                 $attributeName
   *
   * @return self
   */
  public static function fromRequest(Request $request, string $attributeName = 'mysqli'): self
  {
    $mysqli = $request->getAttribute($attributeName);
    if ($mysqli === null || !($mysqli instanceof self)) {
      throw new \RuntimeException(
        'Mysqli could not be found in the server request attributes using the key "'. $attributeName .'".'
      );
    }
    
    return $mysqli;
  }

  private $configs = [];
  
  private $connections = [];
  
  public function __construct(array $configs)
  {
    $this->configs = $configs;
  }
  
  /**
   * Initial connection without connection.
   * 
   * @return mixed
   */
  private function init()
  {
    // initial mysqli without connecting
    $connection = mysqli_init();
    
    // set initial command, set connection encoding
    mysqli_options($connection, MYSQLI_INIT_COMMAND,
      "SET NAMES 'utf8'");
    
    return $connection;
  }
  
  /**
   * Start connection for given $name.
   * 
   * @param string $name
   * @throws \RuntimeException
   * @return mixed
   */
  public function connect(string $name = 'default')
  {
    if(!array_key_exists($name, $this->configs)) {
      throw new \RuntimeException(
        'Mysqli could not found the configuration for "'. $name .'".'
      );
    }
    
    if(!array_key_exists($name, $this->connections)) {
      $config = $this->configs[$name];
      $connection = $this->init();
      mysqli_real_connect($connection,
        $config['host'], $config['username'], $config['password'],
        $config['dbname']
      );
      $this->connections[$name] = $connection;
    }
    
    return $this->connections[$name];
  }

  /**
   * Close connected connections.
   */
  public function close() : void
  {
    foreach($this->connections as $connection) {
      mysqli_close($connection);
    }
  }
}
