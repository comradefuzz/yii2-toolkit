Yii2 toolkit
============
Collection of useful behaviors, widgets etc.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist ladno/yii2-toolkit "*"
```

or add

```
"ladno/yii2-toolkit": "*"
```

to the require section of your `composer.json` file.


Usage
-----

@TBD


Nested models
============
One nested model
-----


In base model:

```
	use NestedModelsBehaviorTrait;

	public function behaviors()
	{
		return [
			'nestedModels' => [
				'class' => 'ladno\yii2toolkit\behaviors\NestedModelsBehavior',
				'models' => [
					'<randomModelCode>' => [
						'class' => '<path\to\YourNestedModelClass>',
						'source' => '<nested_model_attr_in_base_model>',
                        'serialize' => <bool, true/false>,
					]
				]
			],
		];
	}
```

In search model class:

```
$query
    ........
    ->andFilterWhere(['like', '<nested_model_attr_in_base_model>.<nested_model_attr>', $this-><nested_model_attr>])
```

In forms:

```
<?= $form->field($model-><randomModelCode>, '<nested_model_attr>') ?>
```

In detail view:

```
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute' => '<nested_model_attr>',
            'value' => $model-><randomModelCode>-><nested_model_attr>
        ],
......
```

In list:

```
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' =>'<nested_model_attr>',
            'value' => function($model) {
                return $model-><randomModelCode>-><nested_model_attr>;
            },
        ],
......
```


Multiple nested models
-----


In base model:

```
	use NestedModelsBehaviorTrait;

	public function behaviors()
	{
		return [
			'multipleNestedModels' => [
				'class' => 'ladno\yii2toolkit\behaviors\MultipleNestedModelsBehavior',
				'models' => [
					'<randomModelCode>' => [
						'class' => 'ladno\yii2toolkit\models\NestedModelsCollection',
						'source' => '<nested_model_attr_in_base_model>',
						'nestedClass' => '<path\to\YourNestedModelClass>',
						'serialize' => <bool, true/false>,
                        'fabricMethod' => '<methodName>, not define if you don`t use fabric method',
                        'fabricMethodParam' => '<paramName to pass in fabric method>',
					]
				]
			],
		];
	}
```


In forms:

```
foreach ($$model-><randomModelCode> as $num => $object) {?>
    <?=$form->field($object, "[$num]<nested_model_attr>") ?>
<?}
```

In detail view, for example:

```
$attributes = [];
foreach ($model->objectModels as $object)
{
    $attributes[] = $object->attributes;
}
$str = "<pre>".print_r($attributes, TRUE)."</pre>";

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute' => '<nested_model_attr>',
            'format' => "html",
            'value' => $str
        ],
......
```
Translatable Behavior
============

Behavior for translate dynamic data. linked with `lajax/yii2-translate-manager`
Replace values attributes for translate to generated key and set unique ID to special attribute. 
Key format: <prefix>_<class name>_<id attribute>_<attribute name>
For example: dynamic_BlogPost_58130a830eb73_title


Usage
-----

```
[
    'class' => 'ladno\yii2toolkit\behaviors\TranslatableBehavior',
    "attributes" => [
        "<attribute 1 for translate>",
        "<attribute 2 for translate>",
        ......
    ],
    "prefix" => "<your prefix for dynamic keys>",
    "idAttribute" => "<unique item ID>",
],
```

Extension for `lajax/translatemanager`
============

With this extension, `lajax/translatemanager` correctly work with translation dynamic data.
Dynamic attributes of models must be with special prefix. For this, see Translatable Behavior

Configure:

```
'modules' => [
    'translatemanager' => [
        'class' => 'lajax\translatemanager\Module',
        'controllerNamespace' => 'ladno\yii2toolkit\translate',
        'controllerMap' => [
            "language" => [
                'class' => 'ladno\yii2toolkit\translate\LanguageController',
                'excludeKeysPrefix' => '<your prefix for dynamic keys>'
            ]
        ]
    ],
],
```