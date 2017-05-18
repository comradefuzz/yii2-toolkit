<?php

namespace ladno\yii2toolkit\translate;

use ladno\yii2toolkit\translate\Scanner;
use lajax\translatemanager\bundles\ScanPluginAsset;
use lajax\translatemanager\models\LanguageSource;
use yii\data\ArrayDataProvider;

class ScanAction extends \lajax\translatemanager\controllers\actions\ScanAction
{
	public $excludeKeysPrefix;

	/**
	 * @inheritdoc
	 */
	public function init() {

		ScanPluginAsset::register($this->controller->view);
		parent::init();
	}

	/**
	 * Detecting new language elements.
	 * @return string
	 */
	public function run() {

		$scanner = new Scanner;
		$scanner->run($this->excludeKeysPrefix);

		$newDataProvider = $this->controller->createLanguageSourceDataProvider($scanner->getNewLanguageElements());
		$oldDataProvider = $this->_createLanguageSourceDataProvider($scanner->getRemovableLanguageSourceIds());

		return $this->controller->render('scan', [
			'newDataProvider' => $newDataProvider,
			'oldDataProvider' => $oldDataProvider,
		]);
	}

	/**
	 * Returns an ArrayDataProvider consisting of language elements.
	 * @param array $languageSourceIds
	 * @return ArrayDataProvider
	 */
	private function _createLanguageSourceDataProvider($languageSourceIds) {
		$languageSources = LanguageSource::find()->with('languageTranslates')->where(['id' => $languageSourceIds])->all();

		$data = [];
		foreach ($languageSources as $languageSource) {
			$languages = [];
			if ($languageSource->languageTranslates) {
				foreach ($languageSource->languageTranslates as $languageTranslate) {
					$languages[] = $languageTranslate->language;
				}
			}

			$data[] = [
				'id' => $languageSource->id,
				'category' => $languageSource->category,
				'message' => $languageSource->message,
				'languages' => implode(', ', $languages)
			];
		}

		return new ArrayDataProvider([
			'allModels' => $data,
			'pagination' => false,
		]);
	}

}
