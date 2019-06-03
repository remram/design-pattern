<?php
/**
 * Created by PhpStorm.
 * User: ramyhasan
 * Date: 2019-03-12
 * Time: 12:15
 */

namespace ObserverPattern;


class NzzPublisher extends Publisher
{

  /** @var NewsPaper */
  private $currentNewsPaper;

  public function setCurrentNewsPaper(NewsPaper $currentNewsPaper): void
  {
    $this->currentNewsPaper = $currentNewsPaper;

    //Once the news paper is set, all subscriber will be informed
    $this->spreadNewsPaper($currentNewsPaper);
  }

  public function getCurrentNewsPaper(): NewsPaper
  {
    return $this->currentNewsPaper;
  }
}