<?php
/**
 * Created by PhpStorm.
 * User: ramyhasan
 * Date: 2019-03-13
 * Time: 13:56
 */

namespace DecoratorPattern\menu;


abstract class Side implements Dish
{

  /** @var Dish */
  protected $dish;

  public function __construct(Dish $dish)
  {
    $this->dish = $dish;
  }
}