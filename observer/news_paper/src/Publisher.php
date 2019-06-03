<?php
/**
 * Created by PhpStorm.
 * User: ramyhasan
 * Date: 2019-03-12
 * Time: 12:05
 */

namespace ObserverPattern;


abstract class Publisher
{
  /** @var Subscriber $subscriberList array */
  private $subscriberList = [];

  public function addSubscription(Subscriber $subscriber): void
  {
    $this->subscriberList[] = $subscriber;
  }

  public function removeSubscription(int $index): void
  {
    if($this->subscriberList[$index]) {
      unset($this->subscriberList[$index]);
    }
  }

  public function spreadNewsPaper(NewsPaper $newsPaper): void
  {
    /**
     * @var int $index
     * @var Subscriber $subscriber
     */
    foreach ($this->subscriberList as $index => $subscriber) {
      $subscriber->receiveNewsPaper($newsPaper);
    }
  }
}