<?php

namespace ladno\yii2toolkit\behaviors;

use lajax\translatemanager\helpers\Language;
use yii\base\Behavior;
use yii\base\InvalidCallException;
use yii\db\BaseActiveRecord;
use yii\helpers\StringHelper;

/**
 * Зависит от модуля lajax/yii2-translate-manager
 *
 * Чтобы отвязать, нужно реализовать свою логику записи в базу переводов модуля i18n
 * вместо вызова Language::saveMessage. Если использовать файловое хранение данных в i18n,
 * то создать мета-файл, куда записывать Yii::t("<категория>", "<сгенерированный ключ>").
 * И Language::t заменить на Yii::t (Language::t просто добавляет html-форматирование)
 *
 */

class TranslatableBehavior extends Behavior
{
	public $attributes;
	public $prefix = "dynamic";
	public $idAttribute;

	public function events()
	{
		return [
			BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
			BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
		];
	}

	public function beforeInsert()
	{
		$this->setUniqId();

		foreach ($this->attributes as $attribute) {
			$key = $this->generateKey($attribute);
			$this->setGeneratedKey($attribute, $key);
			$this->saveKey($attribute);
		}
	}

	private function setUniqId()
	{
		$this->owner->{$this->idAttribute} = uniqid();
	}

	private function generateKey(string $attribute): string
	{
		return $this->prefix . "_" .
			StringHelper::basename(get_class($this->owner)) . "_" .
			$this->owner->{$this->idAttribute} . "_" .
			$attribute;
	}

	private function setGeneratedKey(string $attribute, string $key)
	{
		$this->owner->{$attribute} = $key;
	}

	private function saveKey($attribute)
	{
		Language::saveMessage($this->owner->{$attribute}, get_class($this->owner));
	}


	public function getTranslate(string $attribute, array $params = [], $language = NULL)
	{
		return \Yii::t(get_class($this->owner), $this->owner->{$attribute}, $params, $language);
	}

	public function beforeUpdate()
	{
		$isReadonlyAttrsChanged = $this->checkForChangeReadonlyAttrs();

		if ($isReadonlyAttrsChanged) {
			throw new InvalidCallException("You must not change this attributes: ".implode(", ", $this->attributes));
		}
	}

	public function checkForChangeReadonlyAttrs(): bool
	{
		$dirtyAttributes = array_keys($this->owner->dirtyAttributes);
		$notChangedAttrs = array_diff($this->attributes, $dirtyAttributes);
		return count($this->attributes) != count($notChangedAttrs);
	}
}