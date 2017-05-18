<?php
/**
 * Created by PhpStorm.
 * User: fuzz
 * Date: 04.10.16
 * Time: 13:00
 */

namespace ladno\yii2toolkit\activeRecordTraits;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;

/**
 * Class NestedModelTrait
 * Expects declared property:
 *      protected $_nestedModels = [
 *          'modelName' => [
 *              'class' => 'modelClassName',
 *              'source' => 'source_property_name'
 *          ]
 *      ]
 *
 * Requires `nestedModelsEventsSubscribe()` in `init()` method
 *
 * @package ladno\yii2toolkit\behaviors
 * @deprecated Moved to ladno\yii2toolkit\nestedModels
 */
trait NestedModelTrait
{
    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {
            foreach ($this->_nestedModels as $target => $config) {
                if (!$this->getNestedModel($target)->load($data, $formName)) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    public function __get($name)
    {
        if (in_array($name, array_keys($this->_nestedModels))) {
            $result = $this->getNestedModel($name);
        } else {
            $result = parent::__get($name);
        }

        return $result;
    }


    protected function nestedModelsEventsSubscribe()
    {
        $this->on(BaseActiveRecord::EVENT_BEFORE_VALIDATE, [$this, 'nestedModelsBeforeValidate']);

        $this->on(BaseActiveRecord::EVENT_BEFORE_INSERT, [$this, 'nestedModelsSerializeAttributes']);
        $this->on(BaseActiveRecord::EVENT_BEFORE_UPDATE, [$this, 'nestedModelsSerializeAttributes']);
    }

    protected function getNestedModel($target)
    {
        if (!isset($this->_nestedModels[$target]['instance'])) {
            $className = $this->_nestedModels[$target]['class'];
            if (!class_exists($className)) {
                throw new InvalidConfigException("Unknown class {$className}");
            }

            $this->_nestedModels[$target]['instance'] = new $className;
            $attributes = $this->deserializeNestedModelAttributes($target);
            if (!is_null($attributes)) {
                $this->_nestedModels[$target]['instance']->setAttributes($attributes);
            }

        }

        return $this->_nestedModels[$target]['instance'];
    }

    protected function deserializeNestedModelAttributes($target)
    {
        $sourceAttribute = $this->_nestedModels[$target]['source'];
        $attributes = null;
        if (!is_null($this->{$sourceAttribute})) {
            $attributes = unserialize($this->{$sourceAttribute});
            if (!is_array($attributes)) {
                throw new ErrorException("Invalid data in {$target} source attribute");
            }
        }

        return $attributes;
    }

    protected function serializeNestedModelAttributes($target)
    {
        $attributes = $this->getNestedModel($target)->attributes;
        return serialize($attributes);
    }

    public function nestedModelsBeforeValidate()
    {
        foreach ($this->_nestedModels as $target => $config) {
            if (!$this->{$target}->validate()) {
                foreach ($this->{$target}->getFirstErrors() as $error) {
                    $this->addError($config['source'], $error);
                }
            }
        }
    }


    public function nestedModelsSerializeAttributes()
    {
        foreach ($this->_nestedModels as $target => $config) {
            $this->{$config['source']} = $this->serializeNestedModelAttributes($target);
        }
    }


}