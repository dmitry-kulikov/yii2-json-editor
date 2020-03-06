<?php

// ensure we get report on all possible PHP errors
error_reporting(-1);

$vendorPath = dirname(__DIR__) . '/vendor';
require_once "$vendorPath/autoload.php";
require_once "$vendorPath/yiisoft/yii2/Yii.php";
