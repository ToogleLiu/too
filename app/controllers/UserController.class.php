<?php

class UserController extends Controller
{
	
	public function loginAction()
	{
		echo "<Pre>";

		print_r(self::$params);
	}
}