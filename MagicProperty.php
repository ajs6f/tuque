<?php

/**
 * @file 
 * This file contains the class MagicProperty
 */

/**
 * This abstract class allows us to implement PHP magic properties by defining a private method in
 * the class that entends it. It attemtps to make the magic properties behave as much like normal
 * PHP properties as possible. 
 * 
 * This code lets the user define a new method that will be called when a property is accessed. Any
 * method that ends in MagicProperty is code that implements a magic property.
 * 
 * Usage Example
 * @code
 * class MyClass extends MagicProperty {
 *   private $secret;  
 * 
 *   protected function myExampleMagicProperty($function, $value) {
 *     switch($function) {
 *       case 'set':
 *         $secret = $value;
 *         return;
 *       case 'get':
 *         return $secret; 
 *       case 'isset':
 *         return isset($secret);
 *       case 'unset':
 *         return unset($secret);
 *     }
 *   }
 * }
 * 
 * $test = new MyClass();
 * $test->myExample = 'woot';
 * print($test->myExample);
 * @endcode
 */
abstract class MagicProperty { 
  
  private function getMagicPropertyMethodName($name) {
    $method = $name . 'MagicProperty';
    return $method;
  }
  
  /**
   * This implements the PHP __get function which is utilized for reading data from inaccessible properties.
   * It wraps it by calling the appropriatly named method in the inherteting class.
   * http://php.net/manual/en/language.oop5.overloading.php
   * 
   * @param $name The name of the function being called.
   * @return The data returned from the property.
   */
  public function __get($name) {
    $method = $this->getMagicPropertyMethodName($name);
    if (method_exists($this, $method)) {
      return $this->$method('get',NULL);
    }
    else {
      // We trigger an error like php would. This helps with debugging.
      $trace = debug_backtrace();
      $class = get_class($trace[0]['object']);
      trigger_error(
        'Undefined property: ' . $class . '::$' . $name .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'] . 'triggered via __get',
        E_USER_NOTICE);
      return NULL;
    }
  }
  
  /**
   * This implments the PHP __isset function which is utilized for testing if data in inaccessable properties 
   * is set. This function calls the approprietly named method in the inhereting class.
   * http://php.net/manual/en/language.oop5.overloading.php
   * 
   * @param $name The name of the function being called.
   * @return If the variable is set.
   */
  public function __isset($name) {
    $method = $this->getMagicPropertyMethodName($name);
    if (method_exists($this, $method)){
      return $this->$method('isset',NULL);
    }
    else return FALSE;
  }
  
  /**
   * This implements the PHP __set function which is utilized for setting inaccessable properties.
   * http://php.net/manual/en/language.oop5.overloading.php
   * 
   * @param $name the property to set
   * @param $value the value it should be set with 
   */
  public function __set($name, $value) {
    $method = $this->getMagicPropertyMethodName($name);
    if (method_exists($this, $method)) {
      $this->$method('set', $value);
    }
    else {
      // else we allow it to be set like a normal property
      $this->$name = $value;
    }
  }
  
  /**
   * This implements the PHP __unset function which is utilized for unsetting inaccessable properties.
   * http://php.net/manual/en/language.oop5.overloading.php
   * 
   * @param $name The property to unset 
   */
  public function __unset($name) {
    $method = $this->getMagicPropertyMethodName($name);
    if (method_exists($this, $method)){
      $this->$method('unset', NULL);
    }
  }
}
