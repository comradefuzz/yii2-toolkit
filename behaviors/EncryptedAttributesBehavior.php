<?php
/**
 * Created by PhpStorm.
 * User: fuzz
 * Date: 07.10.16
 * Time: 23:32
 */

namespace ladno\yii2toolkit\behaviors;


use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class EncryptedAttributesBehavior extends Behavior
{
    public $encryptionKey;
    public $attributes;


    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'decryptAttributes',

            BaseActiveRecord::EVENT_BEFORE_INSERT => 'encryptAttributes',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'encryptAttributes',
        ];
    }

    public function encryptAttributes()
    {
        foreach ($this->attributes as $attribute) {
            if (is_string($this->owner->{$attribute})) {
                $this->owner->{$attribute} = \Yii::$app->security->encryptByKey($this->owner->{$attribute}, $this->encryptionKey);
            }
        }
    }

    public function decryptAttributes()
    {
        foreach ($this->attributes as $attribute) {
            if (is_string($this->owner->{$attribute})) {
                $this->owner->{$attribute} = \Yii::$app->security->decryptByKey($this->owner->{$attribute}, $this->encryptionKey);
            }
        }
    }
}