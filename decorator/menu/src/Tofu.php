<?php
/**
 * Created by PhpStorm.
 * User: ramyhasan
 * Date: 2019-03-13
 * Time: 13:55
 */

namespace DecoratorPattern\menu;


class Tofu implements Dish
{

  public function getPrice()
  {
    return 15.00;
  }

  public function printDescription()
  {
    echo "\n=> Tofu ";
  }
}