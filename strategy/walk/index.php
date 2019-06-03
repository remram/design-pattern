<?php

$vendorDir = dirname(dirname(__FILE__)) . '/../vendor/';
require_once $vendorDir . 'autoload.php';

use StrategyPattern\Husky;
use StrategyPattern\FastWalk;


/** @var Husky $husky */
$husky = new Husky();

$husky->walk();
$husky->setWalk(new FastWalk());
$husky->walk();