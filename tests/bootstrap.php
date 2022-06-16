<?php

// ensure we get report on all possible PHP errors
error_reporting(-1);

$vendorPath = dirname(__DIR__) . '/vendor';
require_once "$vendorPath/autoload.php";
require_once "$vendorPath/yiisoft/yii2/Yii.php";

if (!class_exists('\PHPUnit_Framework_TestCase') && class_exists('\PHPUnit\Framework\TestCase')) {
    class_alias('\PHPUnit\Framework\TestCase', '\PHPUnit_Framework_TestCase');
}
