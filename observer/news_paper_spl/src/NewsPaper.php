<?php
/**
 * Created by PhpStorm.
 * User: ramyhasan
 * Date: 2019-03-12
 * Time: 12:01
 */

namespace SPLObserverPattern;


class NewsPaper
{
  private $title;

  public function __construct($title)
  {
    $this->title = $title;
  }

  public function getTitle()
  {
    return $this->title;
  }
}