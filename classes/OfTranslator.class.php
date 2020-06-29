<?php
require_once('classes/OfException.class.php');
/**
 * Class to translate msg in OF
 *
 */
class OfTranslator
{
	/**
	 * Name of the selected language
	 *
	 * @var string
	 */
	private static $lang='';
	
	/**
	 * Array containing all the translations
	 *
	 * @var array
	 */
	private static $traductions = null;
	
	/**
	 * Return the name of the selected language
	 *
	 * @return string
	 */
	public static function getLang()
	{
		return OfTranslator::$lang;
	}
	
	/**
	 * Select a language
	 *
	 * @param string $langName
	 */
	public static function setLang($langName)
	{
		OfTranslator::$lang = $langName;
		if (file_exists('lang/'.$langName.'.php')) {
			include 'lang/'.$langName.'.php';
			if (isset($lang))
				OfTranslator::$traductions = $lang;
		} else {
			throw new OfException('LANGUAGE "'.$langName.'"NOT AVAILABLE', 'OfTranslation');
		}
	}
	
	/**
	 * Get available languages
	 *
	 * @return string[]
	 */
	public static function getAvailableLangs()
	{
		return array('francais', 'english', 'italiano', 'espanol', 'euskara');
	}
	
	/**
	 * Translate
	 *
	 * @param string $msgId text to translate
	 * @return string
	 */
	public static function tr($msgId)
	{
		if (OfTranslator::$traductions == null)
			throw new OfException('NO LANGUAGE SELECTED', 'OfTranslator');
		if (isset(OfTranslator::$traductions[$msgId]))
			return OfTranslator::$traductions[$msgId];
		else 
			return 'UNKNOWN STRING "'.$msgId.'"';
	}
	
	/**
	 * Return a hashmap of all translation available
	 *
	 * @return array msgId => translation
	 */
	public static function getAllTranslations()
	{
		return OfTranslator::$traductions;
	}
}



?>