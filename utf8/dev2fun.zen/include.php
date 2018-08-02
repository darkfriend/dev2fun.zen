<?php
/**
 * @author dev2fun <darkfriend>
 * @copyright (c) 2018, darkfriend <hi@darkfriend.ru>
 * @version 1.0.0
 */
IncludeModuleLangFile(__FILE__);

\Bitrix\Main\Loader::registerAutoLoadClasses(
    "dev2fun.zen",
    array(
        'Dev2funYandexZen' => 'include.php',
    )
);

if(class_exists('Dev2funYandexZen')) return;

use \Bitrix\Main\Localization\Loc;

class Dev2funYandexZen {

    private static $instance;
    public static $module_id = 'dev2fun.zen';

	/**
	 * Singleton instance
	 * @return self
	 */
	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new Dev2funYandexZen();
		}
		return self::$instance;
	}

	public static function getCategories() {
		$arCategories = array();
		for($i=1;$i<=26;$i++) {
			$arCategories[Loc::getMessage('DEV2FUN_YZEN_CATEGORY_'.$i)] = Loc::getMessage('DEV2FUN_YZEN_CATEGORY_'.$i);
		}
		return $arCategories;
	}

	public static function getOption($name,$serialize=false) {
		$option = \Bitrix\Main\Config\Option::get(self::$module_id,$name);
		if($serialize) $option = unserialize($option);
		return $option;
	}

	public static function getUTM($title=null) {
		$utmSource = self::getOption('utm_source');
		$utmMedium = self::getOption('utm_medium');
		if(!$utmSource||!$utmMedium) return '';
		$utm = [
			'utm_source' => $utmSource,
			'utm_medium' => $utmMedium,
		];
		$utmTerm = self::getOption('utm_term');
		if($utmTerm=='Y' && $title) {
			$utm['utm_term'] = urlencode($title);
		}
		return http_build_query($utm,'','&amp;');
	}

	public static function clearCache() {
		$cachePath = 'dev2fun.zen';
		$obCache = Bitrix\Main\Data\Cache::createInstance();
		return $obCache->cleanDir($cachePath);
	}

	public static function ShowThanksNotice() {
    	global $APPLICATION;
		\CAdminNotify::Add([
			'MESSAGE' => Loc::getMessage('D2F_YANDEXZEN_DONATE_MESSAGE',['#URL#'=>'/bitrix/admin/dev2fun_zen.php?action=settings&tabControl_active_tab=donate']),
			'TAG' => 'dev2fun_yandexzen_update',
			'MODULE_ID' => 'dev2fun.zen',
		]);
	}
}