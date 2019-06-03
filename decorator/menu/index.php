<?php
/**
 * Created by PhpStorm.
 * User: ramyhasan
 * Date: 2019-03-13
 * Time: 13:47
 */

$vendorDir = dirname(dirname(__FILE__)) . '/../vendor/';
require_once $vendorDir . 'autoload.php';

use DecoratorPattern\menu\Salad;
use DecoratorPattern\menu\Fries;
use DecoratorPattern\menu\Steak;
use DecoratorPattern\menu\Tofu;

//Steak + Fries + Salad  > total: 46.3
$dishOrder = new Salad(new Fries(new Steak()));
$dishOrder->printDescription();
$dishTotalPrice = $dishOrder->getPrice();
echo  " > total: {$dishTotalPrice} \n";

//Tofu + Fries + Salad  > total: 26.3
$dishOrder = new Salad(new Fries(new Tofu()));
$dishOrder->printDescription();
$dishTotalPrice = $dishOrder->getPrice();
echo  " > total: {$dishTotalPrice} \n";

//Tofu  > total: 15
$dishOrder = new Tofu();
$dishOrder->printDescription();
$dishTotalPrice = $dishOrder->getPrice();
echo  " > total: {$dishTotalPrice} \n";

//Steak  > total: 35
$dishOrder = new Steak();
$dishOrder->printDescription();
$dishTotalPrice = $dishOrder->getPrice();
echo  " > total: {$dishTotalPrice} \n";

//> Tofu + Salad  > total: 21.8
$dishOrder = new Salad(new Tofu());
$dishOrder->printDescription();
$dishTotalPrice = $dishOrder->getPrice();
echo  " > total: {$dishTotalPrice} \n";