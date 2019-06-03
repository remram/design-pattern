<?php
/**
 * Created by PhpStorm.
 * User: ramyhasan
 * Date: 2019-03-12
 * Time: 12:21
 */

namespace ObserverPattern;


class SubscriberA implements Subscriber
{

  public function receiveNewsPaper(NewsPaper $newsPaper): void
  {
    echo "\n=> Subscriber A has received the current news paper: " . $newsPaper->getTitle() . "\n";
  }
}