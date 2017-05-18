<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 21.10.16
 * Time: 10:54
 */

namespace ladno\yii2toolkit\nestedModels;

use yii\base\InvalidCallException;
use yii\helpers\StringHelper;

class MultipleNestedModelsBehavior extends BaseNestedModelsBehavior
{
	public function validateModels()
	{
		foreach ($this->models as $modelAlias => $config) {
			foreach ($this->{$modelAlias} as $num => $instance) {
				$this->validateModel($instance, $config['source']);
			}
		}
	}

	public function setModelsAttributesToSource()
	{
		foreach ($this->models as $modelAlias => $config) {
			$attributesArray = [];
			foreach ($this->getModel($modelAlias) as $instance) {
				$attributesArray[] = $instance->attributes;
			}

			$this->setAttribute($config['source'], $attributesArray, $config['serialize']);
		}

		return TRUE;
	}


	protected function getModelInstance($modelAlias)
	{
		$modelClassName = $this->getModelClassName($modelAlias);
		$modelInstance  = new $modelClassName;

		$sourceAttributeName = $this->models[$modelAlias]['source'];
		if (!is_null($this->owner->{$sourceAttributeName})) {
			$attributes = $this->getAttribute($sourceAttributeName, $this->models[$modelAlias]['serialize']);
			foreach ($attributes as $num => $item) {
				$nestedModelClassName = $this->models[$modelAlias]['nestedClass'];
                $method = $this->models[$modelAlias]['fabricMethod'];
                $param = $this->models[$modelAlias]['fabricMethodParam'];
                if ($method && $param)
                {
                    $modelInstance[$num] = $nestedModelClassName::{$method}($item[$param]);
                    if (!($modelInstance[$num] instanceof $nestedModelClassName)) {
                        throw new InvalidCallException("Could not instantiate nested model");
                    }
                }
                else
                {
                    $modelInstance[$num] = new $nestedModelClassName();
                }

				$modelInstance[$num]->setAttributes($item, false);
			}
		}
		return $modelInstance;
	}


	public function load($data, $formName = NULL)
	{
        if (is_null($formName)) {
            $formName = $this->owner->formName();
        }

		foreach ($this->models as $modelAlias => $config) {
			$nestedClassName = StringHelper::basename($config['nestedClass']);
			$collection      = $this->{$modelAlias};

			if (!$data[$formName][$modelAlias]) continue;

			foreach ($data[$formName][$modelAlias] as $id => $item) {
				if (!isset($collection[$id])) {
					$collection[$id] = new $config['nestedClass']();
				}

				if (!$collection[$id]->load([$nestedClassName => $item])) {
					return FALSE;
				}
			}
		}

		return TRUE;
	}
}