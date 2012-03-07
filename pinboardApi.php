<?php

require_once 'curl/curl.php';

class PinboardApi
{
  protected $username, $password;
  protected $methods = array(
    'postsUpdate', 
    'postsAdd', 'postsDelete', 'postsGet', 'postsDates', 'postsRecent', 'postsAll', 'postsSuggest', 
    'tagsGet', 'tagsDelete', 'tagsRename', 
    'userSecret'
  );

  public function __construct($username, $password)
  {
    $this->username = $username;
    $this->password = $password;
  }

  protected function getUrl($method)
  {
    return 'https://' . $this->username . ':' . $this->password . '@api.pinboard.in/v1/' . $method;
  }

  public function __call($name, $arguments)
  {
    if (in_array($name, $this->methods))
    {
      $words = preg_split('/(?=[A-Z])/',$name); // split on capital letters
      array_unshift($arguments, strtolower(implode('/', $words)));
      return call_user_func_array(array($this, 'api'), $arguments);
    }

    throw new Exception('Unsupported method: ') . $name;
  }

  protected function api($method, $args = array())
  {
    $format = isset($args['format']) ? $args['format'] : 'xml';
    $raw = false;
    if (isset($args['raw']))
    {
      $raw = $args['raw'];
      unset($args['raw']);
    }

    $url = $this->getUrl($method);
    $curl = new Curl;
    $response = $curl->get($url, $args);

    return $raw ? $response->body : $this->parseResponse($response, $format);
  }

  protected function parseResponse($response, $format)
  {
    if ($response->headers['Status-Code'] != 200)
    {
      return $response->body;
    }

    switch ($format)
    {
      case 'xml':
        return simplexml_load_string($response->body);

      case 'json':
        return json_decode($response->body);

      default:
        throw new InvalidArgumentException('Unsupported format: ' . $format);
    }
  }
}
