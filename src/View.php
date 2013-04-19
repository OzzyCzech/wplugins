<?php
namespace om;
/**
 * Copyright (c) 2013 Roman Ožana (http://omdesign.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 *
 * <code>
 * View::$dir = __DIR__ . '/tamplates/';
 *
 * // simple include phtml file
 * echo View::from('test/test.phtml');
 *
 *
 * // setup some variable
 * $view = View::from('test/test.phtml');
 * $view->variable = 'value';
 *
 * echo $view->variable; // prints 'value'
 *
 * </code>
 *
 * @author Roman Ozana <ozana@omdesign.cz>
 */
class View {

  /** @var array */
  public $vars = array();

  /** @var string */
  public static $dir;

  /** @var $file */
  public $file;

  /**
   * @param $file
   * @return View
   */
  public static function from($file) {
    return new View($file);
  }

  /**
   * @param $file
   */
  public function __construct($file) {
    $this->file = $file;
  }

  /**
   * Render View
   *
   * @throws Exception
   * @internal param $name
   */
  public function render($return = false) {
    extract($this->vars);
    if (
      file_exists($file = $this->file) ||
      file_exists($file = View::$dir . $this->file) ||
      file_exists($file = View::$dir . $this->file . '.phtml') ||
      file_exists($file = View::$dir . $this->file . '.php')
    ) {
      if ($return) ob_start();

      include($file);

      if ($return) {
        $output = ob_get_clean();
        return $output;
      }
    } else {
      throw new \Exception('Template file ' . $file . ' not found');
    }
  }


  /**
   * @param string $name
   */
  public function __get($name) {
    return $this->vars[$name];
  }

  /**
   * @param mixed $name
   * @param mixed $value
   */
  public function __set($name, $value) {
    $this->vars[$name] = $value;
  }

  /**
   * Check if some variable isset
   *
   * @param string $name
   * @return bool
   */
  public function __isset($name) {
    return isset($this->vars[$name]);
  }

  /**
   * Return view as string
   *
   * @return string
   */
  public function __toString() {
    return $this->render(true);
  }
}