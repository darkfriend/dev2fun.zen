<?php
/** @var Dev2funYandexZenComponent $this */
header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
$sourceCharset = 'cp1251';
if(\BX_UTF===true) {
    $sourceCharset = 'utf8';
}
?>
    <rss version="2.0"
         xmlns:content="http://purl.org/rss/1.0/modules/content/"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
         xmlns:media="http://search.yahoo.com/mrss/"
         xmlns:atom="http://www.w3.org/2005/Atom"
         xmlns:georss="http://www.georss.org/georss">
        <channel>
            <?php if (!empty($arResult['SITE']["SITE_NAME"])) { ?>
                <title><?= $APPLICATION->ConvertCharset($arResult['SITE']["SITE_NAME"], $sourceCharset, 'utf8') ?></title>
            <?php } ?>
            <?php if (!empty($arResult['SITE']["SITE_URL"])) { ?>
                <link><?= $APPLICATION->ConvertCharset($arResult['SITE']["SITE_URL"], $sourceCharset, 'utf8') ?></link>
            <?php } ?>
            <?php if (!empty($arResult['SITE']["SITE_DESCRIPTION"])) { ?>
                <description><?= $APPLICATION->ConvertCharset($arResult['SITE']["SITE_DESCRIPTION"], $sourceCharset, 'utf8'); ?></description>
            <?php } ?>
            <?php if (!empty($arResult['SITE']["SITE_LANG"])) { ?>
                <language><?= $APPLICATION->ConvertCharset($arResult['SITE']["SITE_LANG"], $sourceCharset, 'utf8'); ?></language>
            <?php } ?>
            <?php foreach ($arResult["ITEMS"] as $arItem): ?>
                <item>
                    <?php if (!empty($arItem["NAME"])) { ?>
                        <title><?= $APPLICATION->ConvertCharset($arItem["NAME"], $sourceCharset, 'utf8') ?></title>
                    <?php } ?>
                    <?php if (!empty($arItem["DETAIL_PAGE_URL"])) { ?>
                        <link><?= $APPLICATION->ConvertCharset($arItem["DETAIL_PAGE_URL"], $sourceCharset, 'utf8') ?></link>
                    <?php } ?>
                    <?php if (!empty($arItem["DATE_CREATE"])) { ?>
                        <pubDate><?= $APPLICATION->ConvertCharset($arItem["DATE_CREATE"], $sourceCharset, 'utf8') ?></pubDate>
                    <?php } ?>
                    <?php if (!empty($arResult["RATING"])) { ?>
                        <media:rating scheme="urn:simple"><?= $APPLICATION->ConvertCharset($arResult['RATING'], $sourceCharset, 'utf8') ?></media:rating>
                    <?php } ?>
                    <?php if (!empty($arResult['SITE']["SITE_NAME"])) { ?>
                        <author><?= $APPLICATION->ConvertCharset($arResult['SITE']["SITE_NAME"], $sourceCharset, 'utf8'); ?></author>
                    <?php } ?>
                    <?php if (!empty($arResult["CATEGORY"])) { ?>
                        <?php foreach ($arResult["CATEGORY"] as $category): ?>
                            <category><?= $APPLICATION->ConvertCharset($category, $sourceCharset, 'utf8'); ?></category>
                        <?php endforeach; ?>
                    <?php } ?>
                    <?php if (!empty($arItem["MEDIA"])) { ?>
                        <?php foreach ($arItem["MEDIA"] as $media) { ?>
                            <enclosure
                                url="<?= $APPLICATION->ConvertCharset($media['url'], $sourceCharset, 'utf8') ?>"
                                type="<?= $APPLICATION->ConvertCharset($media['type'], $sourceCharset, 'utf8') ?>"
                            />
                        <?php } ?>
                    <?php } ?>
                    <?php if (!empty($arItem["PREVIEW_TEXT"])) { ?>
                        <description><![CDATA[
                            <?= $APPLICATION->ConvertCharset($arItem["PREVIEW_TEXT"], $sourceCharset, 'utf8'); ?>
                            ]]>
                        </description>
                    <?php } ?>
                    <?php if (!empty($arItem["DETAIL_TEXT"])) { ?>
                        <content:encoded><![CDATA[
                            <?= $APPLICATION->ConvertCharset($arItem["DETAIL_TEXT"], $sourceCharset, 'utf8'); ?>
                            ]]>
                        </content:encoded>
                    <?php } ?>
                </item>
            <?php endforeach; ?>
        </channel>
    </rss>
<?php die(); ?>