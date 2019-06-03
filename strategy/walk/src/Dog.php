<?php

namespace StrategyPattern;


abstract class Dog {

  /**
   * @var Walk
   */
	protected $walk;


	public function __construct() {
		$this->walk = new NormalWalk();
	}

	public function setWalk(Walk $walk) {
		$this->walk = $walk;
	}

	public function walk() {
		$this->walk->walk();
	}
}