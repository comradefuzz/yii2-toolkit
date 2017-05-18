<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 21.10.16
 * Time: 10:54
 */

namespace ladno\yii2toolkit\nestedModels;


use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Class NestedModelsBehavior
 * Usage:
 *
 * Main model should use `ladno\yii2toolkit\activeRecordTraits\NestedModelsBehaviorTrait` and have behaviors configured:
 * $behaviors[] = [
 *      'class' => NestedModelsBehavior::className(),
 *      'models' => [
 *          'imageModel' => [
 *              'class' => PostAttachment::className(),
 *              'source' => 'image',
 *          ],
 *      ]
 * ];
 *
 * Target model:
 * class PostAttachment extends yii\base\Model
 * {
 *      public $path;
 *      public $type;
 *
 *      public function rules()
 *      {
 *          return [
 *              [['path', 'type'] , 'string']
 *          ];
 *      }
 * }
 *
 * In form:
 * <?= $form->field($model, 'imageModel[path]')->label("Image path") ?>
 * <?= $form->field($model, 'imageModel[type]')->label("Image type") ?>
 *
 * In view:
 * <?= DetailView::widget([
 *      'model' => $model,
 *      'attributes' => [
 *          '_id',
 *          'account_id',
 *          'imageModel.path',
 *          'imageModel.type',
 *          'status',
 *      ],
 * ])?>
 *
 * @package ladno\yii2toolkit\behaviors
 */
class NestedModelsBehavior extends BaseNestedModelsBehavior
{
    public function init()
    {
        foreach ($this->models as &$modelConfig) {
            $modelConfig = ArrayHelper::merge([
                'serialize' => false
            ], $modelConfig);
        }
    }

    public function validateModels()
    {
        foreach ($this->models as $target => $config) {
            $this->validateModel($this->{$target}, $config['source']);
        }
    }

    public function setModelsAttributesToSource()
    {
        foreach ($this->models as $modelAlias => $config) {
            $this->getModel($modelAlias)->trigger('beforeSetAttributesToSource');
            $this->setAttribute($config['source'], $this->getModel($modelAlias)->attributes, $config['serialize']);
            $this->getModel($modelAlias)->trigger('afterSetAttributesToSource');
        }

        return TRUE;
    }


    protected function getModelInstance($modelAlias)
    {
        $modelClassName = $this->getModelClassName($modelAlias);
        $modelInstance = new $modelClassName;

        if (!($modelInstance instanceof NestedModelInterface)) {
            throw new InvalidConfigException("Nested model should implement `" . NestedModelInterface::class . "`");
        }

        $modelInstance->setParent($this->owner);

        $sourceAttributeName = $this->models[$modelAlias]['source'];
        if (!is_null($this->owner->{$sourceAttributeName})) {
            $attributes = $this->getAttribute($sourceAttributeName, $this->models[$modelAlias]['serialize']);
            $modelInstance->setAttributes($attributes);
        }

        return $modelInstance;
    }


    public function load($data, $formName = NULL)
    {
        foreach ($this->models as $modelAlias => $config) {
            if (is_null($formName)) {
                $formName = $this->owner->formName();
                $data = $data[$formName];
            }

            if (!$this->{$modelAlias}->load($data, $modelAlias)) {
                return FALSE;
            }
        }

        return TRUE;
    }
}