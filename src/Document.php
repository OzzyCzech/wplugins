<?php
namespace om;

/**
 * Copyright (c) 2013 Roman Ožana (http://omdesign.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 *
 * @author Roman Ozana <ozana@omdesign.cz>
 */
abstract class Document extends \stdClass {

	/** @var string */
	public static $prefix;

	/** @var string */
	public static $table;

	/** @var string */
	public static $primary = 'id';

	/**
	 * Return ID value
	 *
	 * @return mixed|null
	 */
	public function getId() {
		return $this->{static::$primary};
	}

	/**
	 * Set ID value
	 *
	 * @param mixed|int $id
	 * @return mixed
	 */
	public function setId($id) {
		return $this->{static::$primary} = $id;
	}

	/**
	 * @param array $data
	 * @param bool $exists
	 * @return $this
	 */
	public function fromArray(array &$data, $exists = true) {
		foreach ($data as $name => $value) {
			$this->{$name} = $value;
		}
		$this->exists($exists);
		return $this;
	}

	public function toArray() {
		return TransformDocument::toArray($this);
	}

	/**
	 * @param $id
	 * @return static
	 */
	public static function fromId($id) {
		$document = new static;
		/** @var Document $document */

		$result = static::wpdb()->get_row(
			sprintf('SELECT * FROM `%s` WHERE `%s` = %d LIMIT 1', static::table(), static::$primary, $id),
			ARRAY_A
		);

		if ($result) {
			$document->fromArray($result);
		}

		return $document;
	}

	/** @var bool */
	protected $exists = false;

	/**
	 * @param null $exists
	 * @return bool
	 */
	public function exists($exists = null) {
		if (is_bool($exists)) $this->exists = $exists;
		return $this->exists;
	}

	/**
	 * @return false|int
	 */
	public function insert() {
		return static::wpdb()->insert(
			static::table(),
			$this->toArray()
		);
	}

	/**
	 * @return false|int
	 */
	public function update() {
		return static::wpdb()->update(
			static::table(),
			$this->toArray(),
			array(static::$primary => $this->getId())
		);
	}


	/**
	 * @return false|int
	 */
	public function delete() {
		return static::wpdb()->delete(
			static::table(),
			array(static::$primary => $this->getId())
		);
	}


	/**
	 * @return bool
	 */
	public function save() {
		return ($this->exists()) ? $this->update() : $this->insert();
	}

	/**
	 * Return table name
	 *
	 * @return string
	 */
	public static function table() {
		if (static::$table) {
			return static::$prefix . static::$table;
		} else {
			$class = explode('\\', get_called_class());
			return static::$prefix . strtolower(array_pop($class));
		}
	}

	/**
	 * Return WPDB data
	 *
	 * @return \wpdb
	 */
	public static function wpdb() {
		return $GLOBALS['wpdb'];
	}
}

class TransformDocument {
	/**
	 * @param Document $document
	 * @return array
	 */
	public static function toArray(Document $document) {
		return get_object_vars($document);
	}
}