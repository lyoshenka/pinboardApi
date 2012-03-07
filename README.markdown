Pinboard API
============

A super-simple PHP wrapper for the [Pinboard API](https://pinboard.in/api/).

    require_once 'pinboardApi.php';
    $api = new PinboardApi('USRENAME', 'PASSWORD');
    $api->postsGet();

