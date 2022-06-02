<?php

declare(strict_types=1);

namespace AndreasHGK\EasyKits\libs\muqsit\invmenu\session;

use AndreasHGK\EasyKits\libs\muqsit\invmenu\InvMenu;
use AndreasHGK\EasyKits\libs\muqsit\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		public InvMenu $menu,
		public InvMenuGraphic $graphic
	){}
}