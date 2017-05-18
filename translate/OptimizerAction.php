<?php

namespace ladno\yii2toolkit\translate;

use ladno\yii2toolkit\translate\Optimizer;

/**
 * Class for optimizing language database.
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class OptimizerAction extends \lajax\translatemanager\controllers\actions\OptimizerAction
{
	public $excludeKeysPrefix;

	/**
     * Removing unused language elements.
     * @return string
     */
    public function run() {
        $optimizer = new Optimizer;
        $optimizer->run($this->excludeKeysPrefix);

        $removedLanguageElements = $optimizer->getRemovedLanguageElements();
        return $this->controller->render('optimizer', [
                    'newDataProvider' => $this->controller->createLanguageSourceDataProvider($removedLanguageElements)
        ]);
    }

}
