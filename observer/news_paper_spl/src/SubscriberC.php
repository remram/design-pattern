<?php
/**
 * Created by PhpStorm.
 * User: ramyhasan
 * Date: 2019-03-12
 * Time: 12:23
 */

namespace SPLObserverPattern;


use SplSubject;

class SubscriberC implements \SplObserver
{

  /**
   * Receive update from subject
   * @link https://php.net/manual/en/splobserver.update.php
   * @param SplSubject $subject <p>
   * The <b>SplSubject</b> notifying the observer of an update.
   * </p>
   * @return void
   * @since 5.1.0
   */
  public function update(SplSubject $subject)
  {
    echo "\n=> Subscriber C has received the current news paper: " . $subject->currentNewsPaper->getTitle() . "\n";
  }
}