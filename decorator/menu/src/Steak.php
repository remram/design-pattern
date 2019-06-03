<?php

namespace DecoratorPattern\menu;


class Steak implements Dish
{

  public function getPrice()
  {
    return 35.00;
  }

  public function printDescription()
  {
    echo "\n=> Steak ";
  }
}