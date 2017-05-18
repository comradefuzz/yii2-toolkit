<?php

namespace ladno\yii2toolkit\translate;

use Yii;
use yii\helpers\Console;
use lajax\translatemanager\models\LanguageSource;

/**
 * Scanner class for scanning project, detecting new language elements
 * 
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class Scanner extends \lajax\translatemanager\services\Scanner
{
	public $excludeKeysPrefix;

	/**
	 * @var array for storing language elements to be translated.
	 */
	private $_languageElements = [];

	/**
	 * @var array for storing removabla LanguageSource ids.
	 */
	private $_removableLanguageSourceIds = [];

	/**
	 * Scanning project for text not stored in database.
	 * @return integer The number of new language elements.
	 * @deprecated since version 1.4
	 */
	public function scanning() {

		return $this->run();
	}

	/**
	 * Scanning project for text not stored in database.
	 *
	 * @param null $excludeKeysPrefix
	 *
	 * @return int The number of new language elements.
	 */
	public function run($excludeKeysPrefix = NULL) {

		$scanTimeLimit = Yii::$app->getModule('translatemanager')->scanTimeLimit;

		if (!is_null($scanTimeLimit)) {
			set_time_limit($scanTimeLimit);
		}

		$this->_initLanguageArrays($excludeKeysPrefix);

		$languageSource = new LanguageSource;
		return $languageSource->insertLanguageItems($this->_languageElements);
	}

	/**
	 * Returns new language elements.
	 * @return array associative array containing the new language elements.
	 */
	public function getNewLanguageElements() {
		return $this->_languageElements;
	}

	/**
	 * Returns removable LanguageSource ids.
	 * @return array
	 */
	public function getRemovableLanguageSourceIds() {
		return $this->_removableLanguageSourceIds;
	}

	/**
	 * Returns existing language elements.
	 * @return array associative array containing the language elements.
	 * @deprecated since version 1.4.2
	 */
	public function getLanguageItems() {

		$this->_initLanguageArrays();

		return $this->_languageElements;
	}

	/**
	 * Initialising $_languageItems and $_removableLanguageSourceIds arrays.
	 */
	private function _initLanguageArrays($excludeKeysPrefix = NULL) {
		$this->_scanningProject();

		$languageSources = LanguageSource::find()->all();

		foreach ($languageSources as $languageSource) {
			if (isset($this->_languageElements[$languageSource->category][$languageSource->message]) ||
				$this->checkForExcludePrefix($languageSource->message, $excludeKeysPrefix)
				) {
				unset($this->_languageElements[$languageSource->category][$languageSource->message]);
			} else {
				$this->_removableLanguageSourceIds[$languageSource->id] = $languageSource->id;
			}
		}
	}

	protected function checkForExcludePrefix(string $message, $excludeKeysPrefix = NULL)
	{
		return explode("_", $message)[0] == $excludeKeysPrefix;
	}

	/**
	 * Scan project for new language elements.
	 */
	private function _scanningProject() {
		foreach ($this->scanners as $scanner) {
			$object = new $scanner($this);
			$object->run('');
		}
	}

	/**
	 * Adding language elements to the array.
	 * @param string $category
	 * @param string $message
	 */
	public function addLanguageItem($category, $message) {
		$this->_languageElements[$category][$message] = true;

		$coloredCategory = Console::ansiFormat($category, [Console::FG_YELLOW]);
		$coloredMessage = Console::ansiFormat($message, [Console::FG_YELLOW]);

		$this->stdout('Detected language element: [ ' . $coloredCategory . ' ] ' . $coloredMessage);
	}

}
