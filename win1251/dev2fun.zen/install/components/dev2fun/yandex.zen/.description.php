<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

$arComponentDescription = array(
	"NAME" => Loc::getMessage("DEV2FUN_YZEN_NAME"),
	"DESCRIPTION" => Loc::getMessage("DEV2FUN_YZEN_DESCRIPTION"),
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "dev2fun",
		"NAME" => "rss"
	),
);
?>