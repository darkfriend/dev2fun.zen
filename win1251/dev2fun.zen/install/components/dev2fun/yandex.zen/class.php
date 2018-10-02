<?php
/**
 * @author darkfriend <hi@darkfriend.ru>
 * @copyright dev2fun
 * @version 1.0.3
 */

class Dev2funYandexZenComponent extends CBitrixComponent {

	public $allowTags = '<a><img><iframe><blockquotes><figure><p><h1><h2><h3><h4><h5><h6><br>';
	public $rating = 'nonadult';

	public function getSite() {
		$arSite = CSite::GetByID(SITE_ID)->Fetch();
		$arSite['SITE_NAME'] = Dev2funYandexZen::getOption('blog_name');
		if($arSite['SITE_NAME']) {
			$arSite['SITE_NAME'] = htmlspecialchars($arSite['SITE_NAME']);
		} else {
			$arSite['SITE_NAME'] = htmlspecialchars($arSite["SITE_NAME"] ? $arSite["SITE_NAME"] : $arSite["NAME"]);
		}
		$desc = Dev2funYandexZen::getOption('blog_description');
		if($desc) $arSite['SITE_DESCRIPTION'] = $desc;
		$arSite["SITE_URL"] =  $this->getAbsoluteUrl();
		$arSite["SITE_LANG"] = LANGUAGE_ID;
		return $arSite;
	}

	public function getProtocol() {
		if(CMain::IsHTTPS()) {
			return 'https://';
		} else {
			return 'http://';
		}
	}

	public function getAbsoluteUrl($url=null) {
		if($url) {
			$arUrl = parse_url($url);
		} else {
			$arUrl = array();
		}
		if(!empty($arUrl['scheme'])) {
			$result = $arUrl['scheme'].'://';
		} else {
			$result = $this->getProtocol();
		}
		if(!empty($arUrl['host'])) {
			$result .= $arUrl['host'];
		} else {
			$result .= $_SERVER['HTTP_HOST'];
		}
		$result .= '/'.ltrim($arUrl['path'],'/');

		if(!empty($arUrl['query'])) {
			$result .= '?'.$arUrl['query'];
		}
		if(!empty($arUrl['fragment'])) {
			$result .= '#'.$arUrl['fragment'];
		}
		return $result;
	}

	public function getTextZenFormat($content){
		preg_match_all('#(<img[\s\S]*>)#U', $content, $matches);

		if(empty($matches[0])) return $content;
		foreach ($matches[0] as $value) {
			$newValue = $value;
			if(preg_match('#src=[\'"](.*?)[\'"]#U',$value,$match)){
				$newValue = str_replace(
					$match[1],
					$this->getAbsoluteUrl($match[1]),
					$value
				);
			}
			$content = str_replace($value, "<figure>".$newValue."</figure>", $content);
		}
		$content = $this->getLinkZenFormat($content);
		return $content;
	}

	public function getLinkZenFormat($content) {
		preg_match_all('#<a.*href\=[\'"](.*?)[\'"].*>#U', $content, $matches);
		if(empty($matches[1])) return $content;
		foreach ($matches[1] as $value) {
			$content = str_replace(
				$value,
				$this->getAbsoluteUrl($value),
				$content
			);
		}
		return $content;
	}

	public function getMedia($content){
		$arMedia = array();
		preg_match_all("#(<img[\s\S]*>)#U", $content, $matches);
		if(empty($matches[0])) return $arMedia;
		foreach ($matches[0] as $key=>$value) {
			if(preg_match('#src=[\'"](.*?)[\'"]#',$value,$match)){
				$arMedia[$key]["url"] = $this->getAbsoluteUrl($match[1]);
				$arMedia[$key]["type"] = image_type_to_mime_type(exif_imagetype($arMedia[$key]["url"]));
			}
		}
		return $arMedia;
	}

	public function getEntity($str) {
		return strtr($str,[
			'&' => '&amp;'
		]);
	}

	public function clearExcess($str) {
		$str = preg_replace('#(\<script.*\>.*?\</script\>)#s','',$str);
		return $str;
	}
}