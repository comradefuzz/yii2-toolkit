<?php
/**
 * Created by PhpStorm.
 * User: fuzz
 * Date: 21.04.17
 * Time: 17:36
 */

namespace ladno\yii2toolkit\nestedModels;

use yii\base\Model;

class NestedModel extends Model implements NestedModelInterface
{
    /** @var Model */
    protected $_parent;

    public function setParent($model)
    {
        $this->_parent = $model;
    }

    public function getParent()
    {
        return $this->_parent;
    }
}