<?php

abstract class App_Base_Registry {
  abstract protected function get($key);
  abstract protected function set($key, $val);
}

?>