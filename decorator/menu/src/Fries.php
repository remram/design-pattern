<?php
/**
 * Created by PhpStorm.
 * User: ramyhasan
 * Date: 2019-03-13
 * Time: 13:59
 */

namespace DecoratorPattern\menu;


class Fries extends Side
{

  public function __construct(Dish $dish)
  {
    parent::__construct($dish);
  }

  public function getPrice()
  {
    return $this->dish->getPrice() + 4.50;
  }

  public function printDescription()
  {
    $this->dish->printDescription();
    echo "+ Fries ";
  }
}