<?php
/**
 * Created by PhpStorm.
 * User: fuzz
 * Date: 11.06.16
 * Time: 14:36
 */

namespace ladno\yii2toolkit\behaviors;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;

/**
 * Stores ActiveRecord array attribute as JSON string
 *
 * @property BaseActiveRecord $owner
 */
class JsonAttributeBehavior extends Behavior
{
    /**
     * @var string[] Attributes you want to be encoded
     */
    public $attributes = [];
    /**
     * @var bool How to decode JSON
     */
    public $asArray = false;
    /**
     * @var array store old attributes
     */
    private $_oldAttributes = [];
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'encodeAttributes',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'encodeAttributes',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'decodeAttributes',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'decodeAttributes',
            BaseActiveRecord::EVENT_AFTER_FIND => 'decodeAttributes',
        ];
    }
    public function encodeAttributes()
    {
        foreach ($this->attributes as $attribute) {
            if (isset($this->_oldAttributes[$attribute])) {
                $this->owner->setOldAttribute($attribute, $this->_oldAttributes[$attribute]);
            }
            $this->owner->$attribute = json_encode($this->owner->$attribute);
        }
    }
    public function decodeAttributes()
    {
        foreach ($this->attributes as $attribute) {
            $this->_oldAttributes[$attribute] = $this->owner->getOldAttribute($attribute);
            $value = json_decode($this->owner->$attribute, $this->asArray);
            $this->owner->setAttribute($attribute, $value);
            $this->owner->setOldAttribute($attribute, $value);
        }
    }
    public function canGetProperty($name, $checkVars = true)
    {
        return in_array($name, array_keys($this->attributes));
    }
    public function __get($name)
    {
        foreach ($this->attributes as $rawAttr => $attr) {
            if ($name == $rawAttr) {
                return $this->_oldAttributes[$attr];
            }
        }
    }
}