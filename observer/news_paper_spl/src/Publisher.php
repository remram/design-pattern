<?php
/**
 * Created by PhpStorm.
 * User: ramyhasan
 * Date: 2019-03-12
 * Time: 12:05
 */

namespace SPLObserverPattern;

use \SplObserver;


abstract class Publisher implements \SplSubject
{

  /**
   * @var int For the sake of simplicity, the Subject's state, essential to
   * all subscribers, is stored in this variable.
   */
  public $state;

  /**
   * @var \SplObjectStorage List of subscribers. In real life, the list of
   * subscribers can be stored more comprehensively (categorized by event
   * type, etc.).
   */
  protected $observers;

  /**
   * Attach an SplObserver
   * @link https://php.net/manual/en/splsubject.attach.php
   * @param SplObserver $observer <p>
   * The <b>SplObserver</b> to attach.
   * </p>
   * @return void
   * @since 5.1.0
   */
  public function attach(SplObserver $observer)
  {
    $this->observers->attach($observer);
  }

  /**
   * Detach an observer
   * @link https://php.net/manual/en/splsubject.detach.php
   * @param SplObserver $observer <p>
   * The <b>SplObserver</b> to detach.
   * </p>
   * @return void
   * @since 5.1.0
   */
  public function detach(SplObserver $observer)
  {
    $this->observers->detach($observer);
  }

  /**
   * Notify an observer
   * @link https://php.net/manual/en/splsubject.notify.php
   * @return void
   * @since 5.1.0
   */
  public function notify()
  {
    /** @var \SplObserver $observer */
    foreach ($this->observers as $observer) {
      $observer->update($this);
    }
  }
}