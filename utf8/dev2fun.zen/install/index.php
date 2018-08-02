<?php
/**
 * Install
 * @author dev2fun (darkfriend)
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

if(class_exists("dev2fun_zen")) return;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Config\Option;

Class dev2fun_zen extends CModule
{
    var $MODULE_ID = "dev2fun.zen";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";

	public function dev2fun_zen() {
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");
        if (isset($arModuleVersion) && is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        } else {
            $this->MODULE_VERSION = '1.0.0';
            $this->MODULE_VERSION_DATE = '2018-08-02 15:00:00';
        }
        $this->MODULE_NAME = Loc::getMessage("DEV2FUN_MODULE_NAME_YANDEXZEN");
        $this->MODULE_DESCRIPTION = Loc::getMessage("DEV2FUN_MODULE_DESCRIPTION_YANDEXZEN");
        $this->PARTNER_NAME = "dev2fun";
        $this->PARTNER_URI = "http://dev2fun.com/";
    }

	public function DoInstall() {
        global $APPLICATION;
        if(!check_bitrix_sessid()) return;
        try {
        	$this->installComponent();
        	$this->installRssScript();
        	$this->installOptions();
            \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
            $APPLICATION->IncludeAdminFile(Loc::getMessage("D2F_YANDEXZEN_STEP1"), __DIR__."/step1.php");
        } catch (Exception $e) {
            $APPLICATION->ThrowException($e->getMessage());
            return false;
        }
        return true;
    }

	public function installOptions() {
		Option::set($this->MODULE_ID,'preview_text_length',200);
		Option::set($this->MODULE_ID,'age_rating','nonadult');
		Option::set($this->MODULE_ID,'tags_allow','<a><img><iframe><blockquotes><figure><p><h1><h2><h3><h4><h5><h6><br>');
		Option::set($this->MODULE_ID,'utm_source','zen');
		Option::set($this->MODULE_ID,'utm_medium','referral');
		Option::set($this->MODULE_ID,'utm_term','Y');
	}

    public function installComponent() {
		if(!CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/{$this->MODULE_ID}/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true)) {
			throw new Exception(Loc::getMessage("ERRORS_INSTALL_COMPONENT"));
		}
	}

	public function installRssScript() {
		if(!CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/{$this->MODULE_ID}/install/yandex.zen", $_SERVER["DOCUMENT_ROOT"].'/yandex.zen')) {
			throw new Exception(Loc::getMessage("ERRORS_INSTALL_RSS_SCRIPT"));
		}
	}

	public function DoUninstall() {
        global $APPLICATION;
        if(!check_bitrix_sessid()) return false;
        try {
			$this->unInstallComponent();
			$this->unInstallRssScript();
			$this->unInstallOptions();
            \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
            $APPLICATION->IncludeAdminFile(Loc::getMessage("D2F_YANDEXZEN_UNSTEP1"), __DIR__."/unstep1.php");
        } catch (Exception $e) {
            $APPLICATION->ThrowException($e->getMessage());
            return false;
        }
        return true;
    }

	public function unInstallOptions() {
		Option::delete($this->MODULE_ID);
	}

	public function unInstallComponent() {
		DeleteDirFilesEx("/bitrix/components/dev2fun/yandex.zen");
	}

	public function unInstallRssScript() {
		DeleteDirFilesEx("/yandex.zen");
	}
}
?>