<?php
/**
 * Created by PhpStorm.
 * User: ramyhasan
 * Date: 2019-03-13
 * Time: 14:02
 */

namespace DecoratorPattern\menu;


class Salad extends Side
{

  public function __construct(Dish $dish)
  {
    parent::__construct($dish);
  }

  public function getPrice()
  {
    return $this->dish->getPrice() + 6.80;
  }

  public function printDescription()
  {
    $this->dish->printDescription();
    echo "+ Salad ";
  }
}