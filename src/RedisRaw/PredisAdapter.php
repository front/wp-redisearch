<?php

namespace WpRediSearch\RedisRaw;

use Predis\Client;

/**
 * Class PredisAdapter
 * @package WPRediSearch\RedisRaw
 *
 * This class wraps the NRK client: https://github.com/nrk/predis
 */
class PredisAdapter extends AbstractRedisRawClient {
  /**
   * @var Client
   */
  public $redis;
  
  public function connect($hostname = '127.0.0.1', $port = 6379, $db = 0, $password = null, $scheme = 'tcp'): RedisRawClientInterface {
    $clientArgs = array();
    if ( $scheme === 'tcp' ) {
      $clientArgs = array(
      'scheme'    => 'tcp',
      'host'      => $hostname,
      'port'      => $port,
      'database'  => $db,
      'password'  => $password,
      );
    } else {
      $clientArgs = array(
        'scheme'    => 'unix',
        'path'      => $host
      );
    }
    $this->redis = new Client( $clientArgs );
    $this->redis->connect();
    return $this;
  }
  
  public function multi(bool $usePipeline = false) {
    return $this->redis->pipeline();
  }
  
  public function rawCommand(string $command, array $arguments) {
    $preparedArguments = $this->prepareRawCommandArguments($command, $arguments);
    $rawResult = $this->redis->executeRaw($preparedArguments);
    return $this->normalizeRawCommandResult($rawResult);
  }
}
