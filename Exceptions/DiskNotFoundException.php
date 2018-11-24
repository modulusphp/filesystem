<?php

namespace Modulus\Filesystem\Exceptions;

use Exception;

class DiskNotFoundException extends Exception
{
  /**
   * __construct
   *
   * @return void
   */
  public function __construct(string $name)
  {
    $args = debug_backtrace()[2];

    foreach ($args as $key => $value) {
      $this->{$key} = $value;
    }

    $this->message = "Could not find disk \"{$name}\"";
  }
}
