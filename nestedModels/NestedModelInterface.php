<?php
/**
 * Created by PhpStorm.
 * User: fuzz
 * Date: 21.04.17
 * Time: 17:29
 */

namespace ladno\yii2toolkit\nestedModels;

interface NestedModelInterface
{
    public function setParent($model);
    public function getParent();
}