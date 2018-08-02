<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Yandex.Zen RSS");
?>
<?$APPLICATION->IncludeComponent(
	"dev2fun:yandex.zen",
	"",
	Array(
		"COUNT" => "100",
		"FILTER_NAME" => "",
		"IBLOCK_ID" => array(),
		"SORT_FIELD" => "timestamp_x",
		"SORT_ORDER" => "desc"
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>