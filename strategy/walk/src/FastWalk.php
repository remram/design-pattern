<?php

namespace StrategyPattern;


class FastWalk implements Walk {
	public function walk() {
		echo "\n->Fast walk\n";
	}
}