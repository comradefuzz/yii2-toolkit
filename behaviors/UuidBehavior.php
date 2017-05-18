<?php
/**
 * Created by PhpStorm.
 * User: fuzz
 * Date: 21.06.16
 * Time: 14:37
 */

namespace ladno\yii2toolkit\behaviors;

use Ramsey\Uuid\Uuid;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

/**
 * Generates uuid for defined attributes on `beforeCreate` in ActiveRecord models
 * @package ladno\yii2toolkit\behaviors
 */
class UuidBehavior extends Behavior
{
    public $attributes;

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeCreate',
        ];
    }

    public function beforeCreate()
    {
        foreach ($this->attributes as $attribute) {
            /**
             * TODO: check if attribute value is unique, regenerate if not
             */
            $this->owner->{$attribute} = $this->generateUuid();
        }
    }

    protected function generateUuid()
    {
        /**
         * TODO: make uuid component configurable via public property
         */
        return Uuid::uuid1()->toString();
    }
}