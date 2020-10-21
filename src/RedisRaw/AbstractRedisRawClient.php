<?php

namespace WpRediSearch\RedisRaw;

abstract class AbstractRedisRawClient implements RedisRawClientInterface {

  public $redis;

  public function connect($hostname = '127.0.0.1', $port = 6379, $db = 0, $password = null): RedisRawClientInterface {
    return $this;
  }
  
  public function flushAll()
  {
    $this->redis->flushAll();
  }
  
  public function multi(bool $usePipeline = false) {
  }
  
  public function rawCommand(string $command, array $arguments){
  }
  
  public function prepareRawCommandArguments(string $command, array $arguments) : array {
    foreach ($arguments as $index => $argument) {
      if (!is_scalar($arguments[$index])) {
        $arguments[$index] = (string)$argument;
      }
    }
    
    array_unshift($arguments, $command);
    return $arguments;
  }


  public function normalizeRawCommandResult($rawResult) {
    return $rawResult === 'OK' ? true : $rawResult;
  }
}
