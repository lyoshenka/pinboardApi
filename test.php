#!/usr/bin/php

<?php

require_once 'pinboardApi.php';
require_once 'auth.php'; // defines $username and $password

$api = new PinboardApi($username, $password);

$api->api('posts/get', array());