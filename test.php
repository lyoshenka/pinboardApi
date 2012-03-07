#!/usr/bin/php

<?php

require_once 'pinboardApi.php';
require_once 'auth.php'; // defines $username and $password

$api = new PinboardApi($username, $password);

var_export($api->postsAdd(array(
  'url' => 'https://github.com/lyoshenka/pinboardApi',
  'description' => 'PinboardApi',
  'tags' => 'github pinboard coolbeans',
  'dt' => date('c', strtotime('03/06/2012'))
)));

//var_export($api->postsGet(array()));
