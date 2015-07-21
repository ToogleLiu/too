<?php

class ResourceFactory
{
	private static $smarty = NULL;

	public static function getSmartyInstance()
	{
		if (self::$smarty == NULL) {

			require PHPLIB_DIR . '/smarty/Smarty.class.php';
			$smarty = new Smarty();
			$smarty->setTemplateDir(SMARTY_TEMPLATE_DIR);
			$smarty->setCompileDir(SMARTY_COMPILE_DIR);
			$smarty->setConfigDir(SMARTY_CONFIG_DIR);
			$smarty->setCacheDir(SMARTY_CACHE_DIR);
			$smarty->addPluginsDir(SMARTY_PLUGIN_DIR);
			$smarty->left_delimiter = SMARTY_LEFT_DELIMITER;
			$smarty->right_delimiter = SMARTY_RIGHT_DELIMITER;

			self::$smarty = $smarty;
			
		}

		return self::$smarty;
	}
}