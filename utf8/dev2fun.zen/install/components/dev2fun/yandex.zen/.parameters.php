<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Localization\Loc;

if(!CModule::IncludeModule("iblock"))
	return;

$arCategories = array();
for($i=1;$i<=26;$i++) {
	$arCategories[Loc::getMessage('DEV2FUN_YZEN_CATEGORY_'.$i)] = Loc::getMessage('DEV2FUN_YZEN_CATEGORY_'.$i);
}

$arSortFields = array(
   "created" => Loc::getMessage('DEV2FUN_YZEN_PROP_CREATED'),
   "timestamp_x" => Loc::getMessage('DEV2FUN_YZEN_PROP_TIMESTAMP_X'),
   "active_from" => Loc::getMessage('DEV2FUN_YZEN_PROP_ACTIVE_FROM'),
   "sort" => Loc::getMessage('DEV2FUN_YZEN_PROP_SORT'),
   "id" => Loc::getMessage('DEV2FUN_YZEN_PROP_ID'),
);

$arSortOrder = array(
	"ASC" => Loc::getMessage('DEV2FUN_YZEN_PROP_ASC'),
	"DESC" => Loc::getMessage('DEV2FUN_YZEN_PROP_DESC'),
);

$arIblocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"]));
while($arRes = $db_iblock->Fetch())
    $arIblocks[$arRes["ID"]] = "[".$arRes["ID"]."] ".$arRes["NAME"];

$arComponentParameters = array(
	"PARAMETERS" => array(
        "IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("DEV2FUN_YZEN_PROP_IBLOCK_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arIblocks,
            "DEFAULT" => '',
			"MULTIPLE" => "Y",
            "ADDITIONAL_VALUES" => "Y",
		),
		"COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("DEV2FUN_YZEN_PROP_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => '100',
		),
		"FILTER_NAME" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("DEV2FUN_YZEN_PROP_FILTER_NAME"),
			"TYPE" => "STRING",
		),
        "SORT_FIELD" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("DEV2FUN_YZEN_PROP_SORT_FIELD"),
            "TYPE" => "LIST",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "Y",
        ),
        "SORT_ORDER" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("DEV2FUN_YZEN_PROP_SORT_ORDER"),
            "TYPE" => "LIST",
            "VALUES" => $arSortOrder,
        ),
	),
);