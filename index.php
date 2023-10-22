<?php
$url = 'http://www.website.com/id/60/EZAEZ////';
$base_url = '/http:\/\/www.website.com\/id\//';
$empty = '';
$pattern = '/\/+$/';
$urn = preg_replace($base_url, '', $url);
$sanitized = preg_replace($pattern, '', $urn);
$params = explode('/', $sanitized);
print_r($params);
require __DIR__ . '/vendor/autoload.php';