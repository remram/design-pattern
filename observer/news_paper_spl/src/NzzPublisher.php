<?php
/**
 * Created by PhpStorm.
 * User: ramyhasan
 * Date: 2019-03-12
 * Time: 12:15
 */

namespace SPLObserverPattern;


class NzzPublisher extends Publisher
{

  public function __construct()
  {
    $this->observers = new \SplObjectStorage();
  }

  public function setCurrentNewsPaper(NewsPaper $currentNewsPaper): void
  {
    $this->currentNewsPaper = $currentNewsPaper;

    //Once the news paper is set, all subscriber will be informed
    $this->notify();
  }

  public function getCurrentNewsPaper(): NewsPaper
  {
    return $this->currentNewsPaper;
  }
}