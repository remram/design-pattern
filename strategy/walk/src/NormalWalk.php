<?php

namespace StrategyPattern;


class NormalWalk implements Walk {
	public function walk() {
		echo "\n->Normal walk\n";
	}
}