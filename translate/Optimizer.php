<?php

namespace ladno\yii2toolkit\translate;

use ladno\yii2toolkit\translate\Scanner;
use lajax\translatemanager\models\LanguageSource;
use yii\helpers\Console;

/**
 * Optimizer class for optimizing database tables
 * 
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class Optimizer extends \lajax\translatemanager\services\Optimizer
{
    /**
     * @var Scanner
     */
    private $_scanner;

    /**
     * @var array a Current language elements in the translating system
     */
    private $_languageElements = [];

    /**
     * Removing unused language elements from database.
     * @return integer The number of removed language elements.
     */
    public function run($excludeKeysPrefix = NULL) {

        $this->_scanner = new Scanner;
        $this->_scanner->run($excludeKeysPrefix);
        $this->_scanner->stdout('Deleted language elements - BEGIN', Console::FG_RED);

        $languageSourceIds = $this->_scanner->getRemovableLanguageSourceIds();

        $this->_initLanguageElements($languageSourceIds);

        LanguageSource::deleteAll(['id' => $languageSourceIds]);

        $this->_scanner->stdout('Deleted language elements - END', Console::FG_RED);

        return count($languageSourceIds);
    }


    /**
     * Initializing $_languageElements array.
     * @param array $languageSourceIds
     */
    private function _initLanguageElements($languageSourceIds) {
        $languageSources = LanguageSource::findAll(['id' => $languageSourceIds]);
        foreach ($languageSources as $languageSource) {
            $this->_languageElements[$languageSource->category][$languageSource->message] = $languageSource->id;

            $category = Console::ansiFormat($languageSource->category, [Console::FG_RED]);
            $message = Console::ansiFormat($languageSource->message, [Console::FG_RED]);

            $this->_scanner->stdout('category: ' . $category . ', message: ' . $message);
        }
    }

}
