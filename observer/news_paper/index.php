<?php

$vendorDir = dirname(dirname(__FILE__)) . '/../vendor/';
require_once $vendorDir . 'autoload.php';

use ObserverPattern\NzzPublisher;
use ObserverPattern\NewsPaper;

/** @var NzzPublisher $publisher */
$publisher = new NzzPublisher();

$publisher->addSubscription(new \ObserverPattern\SubscriberA());
$publisher->addSubscription(new \ObserverPattern\SubscriberC());

$publisher->setCurrentNewsPaper(new NewsPaper('Nzz Media group bought Architonic AG'));

$publisher->removeSubscription(0);
$publisher->addSubscription(new \ObserverPattern\SubscriberB());

$publisher->setCurrentNewsPaper(new  NewsPaper('Nzz is making money like crazy in 2018'));