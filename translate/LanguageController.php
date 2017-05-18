<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 28.10.16
 * Time: 14:01
 */

namespace ladno\yii2toolkit\translate;


class LanguageController extends \lajax\translatemanager\controllers\LanguageController
{
	public $excludeKeysPrefix;

	public function actions()
	{
		$actions = parent::actions();
		$actions['scan'] = [
			'class' => 'ladno\yii2toolkit\translate\ScanAction',
			'excludeKeysPrefix' => $this->excludeKeysPrefix,
		];
		$actions['optimizer'] = [
			'class' => 'ladno\yii2toolkit\translate\OptimizerAction',
			'excludeKeysPrefix' => $this->excludeKeysPrefix,
		];

		return $actions;
	}
}