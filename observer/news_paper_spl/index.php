<?php

$vendorDir = dirname(dirname(__FILE__)) . '/../vendor/';
require_once $vendorDir . 'autoload.php';

use SPLObserverPattern\NzzPublisher;
use SPLObserverPattern\NewsPaper;

/** @var NzzPublisher $publisher */
$publisher = new NzzPublisher();

$subscriberA = new \SPLObserverPattern\SubscriberA();
$subscriberB = new \SPLObserverPattern\SubscriberB();
$subscriberC = new \SPLObserverPattern\SubscriberC();

$publisher->attach($subscriberA);
$publisher->attach($subscriberC);

$publisher->setCurrentNewsPaper(new NewsPaper('Nzz Media group bought Architonic AG'));

$publisher->detach($subscriberA);
$publisher->attach($subscriberB);

$publisher->setCurrentNewsPaper(new  NewsPaper('Nzz is making money like crazy in 2018'));