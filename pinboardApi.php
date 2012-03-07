<?php

require_once 'curl/curl.php';

class PinboardApi
{

  protected $username, $password;

  public function __construct($username, $password)
  {
    $this->username = $username;
    $this->password = $password;
  }

  protected function getUrl($method)
  {
    return 'https://' . $this->username . ':' . $this->password . '@api.pinboard.in/v1/' . $method;
  }

  public function api($method, $args)
  {
    $url = $this->getUrl($method);
    $curl = new Curl;
    $response = $curl->get($url, $args);
    echo $response . "\n";
  }
}
