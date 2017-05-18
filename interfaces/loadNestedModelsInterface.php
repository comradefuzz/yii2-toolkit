<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 21.10.16
 * Time: 18:51
 */

namespace ladno\yii2toolkit\interfaces;


interface loadNestedModelsInterface
{
	public function load($data, $formName = NULL);
}