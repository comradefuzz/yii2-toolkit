<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 21.10.16
 * Time: 10:54
 */

namespace ladno\yii2toolkit\nestedModels;

trait NestedModelsBehaviorTrait
{
	public function load($data, $formName = NULL)
	{
		if (parent::load($data, $formName)) {
			foreach ($this->behaviors as $behavior) {
				if ($behavior instanceof BaseNestedModelsBehavior) {
					$behavior->load($data, $formName);
				}
			}
			return TRUE;
		}

		return FALSE;
	}
}