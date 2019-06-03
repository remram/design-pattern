<?php
/**
 * Created by PhpStorm.
 * User: ramyhasan
 * Date: 2019-03-12
 * Time: 11:58
 */

namespace ObserverPattern;


interface Subscriber
{
  public function receiveNewsPaper(NewsPaper $newsPaper): void;
}