<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var Dev2funYandexZenComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

/**
 * @author darkfriend <hi@darkfriend.ru>
 * @version 1.0.2
 */

use Bitrix\Main\Context,
	Bitrix\Main\Type\DateTime,
	Bitrix\Main\Loader,
	Bitrix\Iblock;

$APPLICATION->RestartBuffer();

if(empty($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600*2;

$obCache = Bitrix\Main\Data\Cache::createInstance();
$cachePath = '/dev2fun.zen/'.SITE_ID.'/';
$cacheId = 'dev2fun.yandex.zen_'.SITE_ID;

if($obCache->initCache($arParams["CACHE_TIME"],$cacheId,$cachePath)){
	$arResult = $obCache->getVars();
} elseif($obCache->startDataCache($arParams["CACHE_TIME"],$cacheId,$cachePath)) {

	if(!\Bitrix\Main\Loader::includeModule("iblock")) {
		$obCache->abortDataCache();
		die(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	}

	if(!\Bitrix\Main\Loader::includeModule("dev2fun.zen")) {
		$obCache->abortDataCache();
		die('Component dev2fun.zen is not installed!');
	}

	$arResult['SITE'] = $this->getSite();

	$arParams["TRUNCATE_LEN"] = Dev2funYandexZen::getOption('preview_text_length');
	$arParams["ALLOW_TAGS"] = Dev2funYandexZen::getOption('tags_allow');
	$arResult['CATEGORY'] = Dev2funYandexZen::getOption('blog_categories',true);
	$arResult['RATING'] = Dev2funYandexZen::getOption('age_rating');

	$arFilter = array(
		"IBLOCK_LID" => SITE_ID,
		"IBLOCK_ACTIVE" => "Y",
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
	);
	if($arParams["CHECK_DATES"])
		$arFilter["ACTIVE_DATE"] = "Y";
	if(count($arParams["IBLOCK_ID"]) > 0)
		$arFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
	if(count($arParams["ELEMENT_ID"]) > 0)
		$arFilter["ID"] = $arParams["ELEMENT_ID"];

	if(!empty($arParams['FILTER_NAME']) && isset($GLOBALS[$arParams['FILTER_NAME']])) {
		$arFilter = array_merge(
			$arFilter,
			$GLOBALS[$arParams['FILTER_NAME']]
		);
	}


	$arSelect = array(
		"ID",
		"NAME",
		"IBLOCK_ID",
		"IBLOCK_SECTION_ID",
		"DETAIL_TEXT",
		"DETAIL_TEXT_TYPE",
		"PREVIEW_TEXT",
		"PREVIEW_TEXT_TYPE",
		"DETAIL_PICTURE",
		"PREVIEW_PICTURE",
		"TIMESTAMP_X",
		"ACTIVE_FROM",
		"LIST_PAGE_URL",
		"DETAIL_PAGE_URL",
		"DATE_CREATE",
	);

	$arSort = array();
	if(!empty($arParams['SORT_FIELD'])) {
		if(!empty($arParams['SORT_ORDER'])) {
			$arSort[$arParams['SORT_FIELD']] = $arParams['SORT_ORDER'];
		} else {
			$arSort[$arParams['SORT_FIELD']] = 'ASC';
		}
	}

	$rsElement = CIBlockElement::GetList(
		$arSort,
		$arFilter,
		false,
		array(
			'nTopCount' => $arParams["COUNT"]
		),
		$arSelect
	);
	$rsElement->SetUrlTemplates($arParams["DETAIL_URL"], "", $arParams["IBLOCK_URL"]);
	$obParser = new CTextParser;

	while($obElement = $rsElement->GetNextElement()) {
		$arItem = $obElement->GetFields();

		$arItem['NAME'] = $this->getEntity($arItem['NAME']);

		$ipropValues = new Iblock\InheritedProperty\ElementValues($arItem["IBLOCK_ID"], $arItem["ID"]);
		$arItem["IPROPERTY_VALUES"] = $ipropValues->getValues();

		Iblock\Component\Tools::getFieldImageData(
			$arItem,
			array('PREVIEW_PICTURE', 'DETAIL_PICTURE'),
			Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT,
			'IPROPERTY_VALUES'
		);

    $arItem["MEDIA"] = [];
		if(!empty($arItem['DETAIL_PICTURE'])) {
      $arItem["MEDIA"][] = [
        'url' => $this->getAbsoluteUrl($arItem['DETAIL_PICTURE']['SRC']),
        'type' => $arItem['DETAIL_PICTURE']['CONTENT_TYPE'],
      ];
    } elseif(!empty($arItem['PREVIEW_PICTURE'])) {
      $arItem["MEDIA"][] = [
        'url' => $this->getAbsoluteUrl($arItem['PREVIEW_PICTURE']['SRC']),
        'type' => $arItem['PREVIEW_PICTURE']['CONTENT_TYPE'],
      ];
    }

		if(!empty($arItem["DETAIL_TEXT"])) {
			$arItem["MEDIA"] = array_merge($arItem["MEDIA"],$this->getMedia($arItem["DETAIL_TEXT"]));
			$arItem["DETAIL_TEXT"] = $this->getTextZenFormat($arItem["DETAIL_TEXT"]);
			$arItem["DETAIL_TEXT"] = $this->clearExcess($arItem["DETAIL_TEXT"]);
			$arItem["DETAIL_TEXT"] = strip_tags($arItem["DETAIL_TEXT"],$arParams["ALLOW_TAGS"]);
		}

		if(empty($arItem['PREVIEW_TEXT']) && !empty($arItem["DETAIL_TEXT"])) {
			$arItem["PREVIEW_TEXT"] = $arItem["DETAIL_TEXT"];
		}
		if(!empty($arItem['PREVIEW_TEXT'])) {
			$arItem["PREVIEW_TEXT"] = $this->clearExcess($arItem["PREVIEW_TEXT"]);
			$arItem["PREVIEW_TEXT"] = $this->getTextZenFormat($arItem["PREVIEW_TEXT"]);
			$arItem["PREVIEW_TEXT"] = strip_tags($arItem["PREVIEW_TEXT"], $arParams["ALLOW_TAGS"]);
			$arItem["PREVIEW_TEXT"] = $obParser->html_cut($arItem["PREVIEW_TEXT"], $arParams["TRUNCATE_LEN"]);
		}

		$arItem["DETAIL_PAGE_URL"] = $this->getAbsoluteUrl($arItem["DETAIL_PAGE_URL"]);
//		if($arItem["DETAIL_PAGE_URL"]) {
//			$utm = Dev2funYandexZen::getUTM($arItem["NAME"]);
//			if($utm) $arItem["DETAIL_PAGE_URL"] .= '?'.$utm;
//		}

		if(!empty($arItem["DATE_CREATE"])) {
			$arItem["DATE_CREATE"] = (new \DateTime($arItem["DATE_CREATE"]))->format('D, d M y H:i:s O');
		}

		$arResult['ITEMS'][] = $arItem;
	}

	$obCache->endDataCache($arResult);
}
$this->includeComponentTemplate();
return;