<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/5/18
 * Time: 下午11:42
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */
echo '<pre>';

$composer = include __DIR__ . '/../vendor/autoload.php';

$request = \FastD\Http\Request::createRequestHandle();

$cookies = $request->cookies;
print_r($cookies);
//$cookies->setCookie('name', 'janhuang');
$cookies->clearCookie('name');

//$cookies->setCookie('name', 'janhuang', 3600, '/');
//print_r($cookies->getCookie('name'));


//print_r($cookies->get('mz_id'));