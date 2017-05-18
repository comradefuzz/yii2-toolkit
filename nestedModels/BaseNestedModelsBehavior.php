<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 21.10.16
 * Time: 10:54
 */

namespace ladno\yii2toolkit\nestedModels;

use ladno\yii2toolkit\interfaces\loadNestedModelsInterface;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\BaseActiveRecord;

abstract class BaseNestedModelsBehavior extends Behavior implements loadNestedModelsInterface
{
	public $models;

	public function canGetProperty($name, $checkVars = TRUE)
	{
		if (parent::canGetProperty($name, $checkVars)) {
			return TRUE;
		} else {
			return in_array($name, array_keys($this->models));
		}
	}

	public function __get($name)
	{
		if (in_array($name, array_keys($this->models))) {
			return $this->getModel($name);
		} else {
			return parent::__get($name);
		}
	}

	public function events()
	{
		return [
			BaseActiveRecord::EVENT_BEFORE_VALIDATE => [$this, 'validateModels'],
			BaseActiveRecord::EVENT_BEFORE_INSERT   => [$this, 'setModelsAttributesToSource'],
			BaseActiveRecord::EVENT_BEFORE_UPDATE   => [$this, 'setModelsAttributesToSource'],
		];
	}

	abstract public function validateModels();

	protected function validateModel(Model $instance, $attribute)
	{
		if (!$instance->validate()) {
			foreach ($instance->getFirstErrors() as $error) {
                $this->owner->addError($attribute, $error);
			}
		}
	}

	abstract public function setModelsAttributesToSource();

	protected function setAttribute(string $sourceAttribute, array $attributes = [], bool $serialize = FALSE)
	{
		$this->owner->{$sourceAttribute} = $serialize ? serialize($attributes) : $attributes;
	}

	protected function getAttribute(string $sourceAttribute, bool $serialize = FALSE)
	{
		$attribute = $this->owner->{$sourceAttribute};

		return $serialize ? unserialize($attribute) : $attribute;
	}

	abstract protected function getModelInstance($modelAlias);

	protected function getModelClassName($modelAlias)
	{
		$modelClassName = $this->models[$modelAlias]['class'];
		if (!class_exists($modelClassName)) {
			throw new InvalidConfigException("Unknown class {$modelClassName}");
		}

		return $modelClassName;
	}

    /**
     * @param $modelAlias
     *
     * @return null|Model
     */
	public function getModel($modelAlias)
	{
		if (in_array($modelAlias, array_keys($this->models))) {
			if (!isset($this->models[$modelAlias]['instance'])) {
				$this->models[$modelAlias]['instance'] = $this->getModelInstance($modelAlias);
			}

			return $this->models[$modelAlias]['instance'];
		}

		return NULL;
	}


	abstract public function load($data, $formName = NULL);
}