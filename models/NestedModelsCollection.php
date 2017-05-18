<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 20.10.16
 * Time: 10:52
 */

namespace ladno\yii2toolkit\models;


use ArrayAccess;
use IteratorAggregate;
use yii\base\ArrayAccessTrait;

class NestedModelsCollection implements ArrayAccess, IteratorAggregate
{
	use ArrayAccessTrait;

	public $data = [];

	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->data[] = $value;
		} else {
			$this->data[$offset] = $value;
		}
	}
}