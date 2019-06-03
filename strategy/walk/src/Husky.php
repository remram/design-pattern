<?php

namespace StrategyPattern;


class Husky extends Dog {
	public function __construct()
  {
    parent::__construct();
    $this->setWalk(new NormalWalk());
  }
}