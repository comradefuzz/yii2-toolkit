<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace ladno\yii2toolkit\widgets;

use Yii;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/**
 *
 */
class FormErrors extends \yii\bootstrap\Widget
{
    public $class = 'alert-danger';
    /**
     * @var Model model class
     */
    public $model;

    /**
     * @var array Excluded model attributes
     */
    public $exclude = [];

    /**
     * @var array the options for rendering the close button tag.
     */
    public $closeButton = [];

    public function init()
    {
        parent::init();

        if (empty($this->options['class'])) {
            $this->options['class'] = $this->class;
        }

        $messages = [];
        if ($this->model->hasErrors()) {
            foreach ($this->model->firstErrors as $attribute => $error) {
                if (!in_array($attribute, $this->exclude)) {
                    $messages[] = "{$error}";
                }
            }
        }

        if ($messages) {
            echo \yii\bootstrap\Alert::widget([
                'body' => Html::ul($messages),
                'closeButton' => $this->closeButton,
                'options' => $this->options,
            ]);
        }
    }
}