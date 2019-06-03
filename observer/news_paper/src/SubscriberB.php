<?php
/**
 * Created by PhpStorm.
 * User: ramyhasan
 * Date: 2019-03-12
 * Time: 12:23
 */

namespace ObserverPattern;


class SubscriberB implements Subscriber
{

  public function receiveNewsPaper(NewsPaper $newsPaper): void
  {
    echo "\n=> Subscriber B has received the current news paper: " . $newsPaper->getTitle() . "\n";
  }
}