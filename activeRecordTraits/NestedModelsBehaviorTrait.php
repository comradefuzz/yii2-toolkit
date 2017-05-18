<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 21.10.16
 * Time: 10:54
 */

namespace ladno\yii2toolkit\activeRecordTraits;


use ladno\yii2toolkit\interfaces\loadNestedModelsInterface;

/**
 * Class NestedModelsBehaviorTrait
 * @package ladno\yii2toolkit\activeRecordTraits
 * @deprecated Moved to ladno\yii2toolkit\nestedModels
 */
trait NestedModelsBehaviorTrait
{
	public function load($data, $formName = NULL)
	{
		if (parent::load($data, $formName)) {
			foreach ($this->behaviors as $behavior) {
				if ($behavior instanceof loadNestedModelsInterface) {
					$behavior->load($data, $formName);
				}
			}
			return TRUE;
		}

		return FALSE;
	}
}