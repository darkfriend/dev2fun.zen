<?
//header('Content-Type: application/rss+xml; charset=utf-8');
header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:georss="http://www.georss.org/georss">
<channel>
	<title><?=$arResult['SITE']["SITE_NAME"];?></title>
	<link><?=$arResult['SITE']["SITE_URL"];?></link>
    <? if(!empty($arResult['SITE']["SITE_DESCRIPTION"])) {?>
	    <description><?=$arResult['SITE']["SITE_DESCRIPTION"];?></description>
    <? } ?>
	<language><?=$arResult['SITE']["SITE_LANG"];?></language>
	<?foreach ($arResult["ITEMS"] as $arItem):?>
		<item>
			<title><?=$arItem["NAME"]?></title>
			<link><?=$arItem["DETAIL_PAGE_URL"]?></link>
			<pubDate><?=$arItem["DATE_CREATE"]?></pubDate>
			<media:rating scheme="urn:simple"><?=$arResult['RATING']?></media:rating>
			<author><?=$arResult['SITE']["SITE_NAME"];?></author>
			<?foreach ($arResult["CATEGORY"] as $category):?>
			    <category><?=$category?></category>
			<?endforeach;?>
			<?foreach ($arItem["MEDIA"] as $media){?>
			    <enclosure url="<?=$media['url']?>" type="<?=$media['type']?>"/>
			<? } ?>
			<?if(!empty($arItem["PREVIEW_TEXT"])){?>
                <description><![CDATA[
                    <?=$arItem["PREVIEW_TEXT"];?>
                ]]></description>
			<?}?>
			<content:encoded><![CDATA[
				<?=$arItem["DETAIL_TEXT"];?>
			]]></content:encoded>
		</item>
	<?endforeach;?>
</channel>
</rss>
<? die(); ?>