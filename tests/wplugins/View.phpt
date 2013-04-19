<?php
require __DIR__ . '/../bootstrap.php';

$view = \om\View::$dir = __DIR__;


try {
  $view = \om\View::from('no exists file');
  $view->render();
  \Tester\Assert::fail('File not exists Exception expected.');
} catch (\Exception $e) {
  \Tester\Assert::true(true);
}


