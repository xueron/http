<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/3/21
 * Time: 下午11:03
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/../vendor/autoload.php';

use FastD\Http\Request;

$request = Request::createRequestHandle();

echo '<pre>';
print_r($request);