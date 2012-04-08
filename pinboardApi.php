<?php

/**
 *
 * PinboardApi
 *
 * A PHP wrapper around the Pinboard api
 *
 * @method mixed postsUpdate
 * @method mixed postsAdd
 * @method mixed postsDelete
 * @method mixed postsGet
 * @method mixed postsDates
 * @method mixed postsRecent
 * @method mixed postsAll
 * @method mixed postsSuggest
 * @method mixed tagsGet
 * @method mixed tagsDelete
 * @method mixed tagsRename
 * @method mixed userSecret
 *
 */
class PinboardApi
{
  protected $username, $password, $curl;
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

  protected function doCurl($url, $args)
  {
    $url = $url . '?' . http_build_query($args);
    $ch = curl_init();
    curl_setopt_array($ch, array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_USERAGENT => 'PinboardApi (https://github.com/lyoshenka/pinboardApi)',
      CURLOPT_URL => $url
    ));

    $response = array(
      'body' => curl_exec($ch), 
      'info' => curl_getinfo($ch),
    );

    if (curl_errno($ch))
    {
      throw new RuntimeException('Curl error: ' . $curl_error($ch));
    }

    curl_close($ch);
    return $response;
  }

  public function __call($name, $arguments)
  {
    if (in_array($name, $this->methods))
    {
      $words = preg_split('/(?=[A-Z])/',$name); // split on capital letters
      array_unshift($arguments, strtolower(implode('/', $words)));
      return call_user_func_array(array($this, 'api'), $arguments);
    }

    throw new Exception('Unsupported method: ' . $name);
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
    $response = $this->doCurl($url, $args);

    return $raw ? $response['body'] : $this->parseResponse($response, $format);
  }

  protected function parseResponse($response, $format)
  {
    if ($response['info']['http_code'] != 200)
    {
      return $response['body'];
    }

    switch ($format)
    {
      case 'xml':
        return simplexml_load_string($response['body']);

      case 'json':
        return json_decode($response['body'], true);

      default:
        throw new InvalidArgumentException('Unsupported format: ' . $format);
    }
  }
}
