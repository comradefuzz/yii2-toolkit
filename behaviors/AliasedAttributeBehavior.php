<?php
/**
 * Created by PhpStorm.
 * User: fuzz
 * Date: 11.06.16
 * Time: 14:36
 */

namespace common\behaviors;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;

/**
 *
 * @property BaseActiveRecord $owner
 */
class AliasedAttributeBehavior extends Behavior
{
    /**
     * @var array Attributes you want to be aliased
     * 
     */
    public $attributes = [];
    
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'mapAttributes',
        ];
    }
    
    public function mapAttributes()
    {
        foreach ($this->attributes as $config) {
            if (is_string($this->owner->{$config['attribute']})) {
                if ($value = array_search($this->owner->{$config['attribute']}, $config['map'])) {
                    $this->owner->{$config['attribute']} = $value;
                }
            }
        }
    }
}