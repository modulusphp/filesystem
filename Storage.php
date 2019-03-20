<?php

namespace Modulus\Filesystem;

use Modulus\Http\Rest;
use Modulus\Support\Extendable;
use Modulus\Support\Filesystem;
use Modulus\Filesystem\Exceptions\DiskNotFoundException;

class Storage
{
  use Extendable;

  /**
   * $disk
   *
   * @var string
   */
  protected $disk;

  /**
   * $path
   *
   * @var string
   */
  protected $path;

  /**
   * __construct
   *
   * @param string $name
   * @return void
   */
  public function __construct(string $name)
  {
    $disks = config('filesystems.disks');

    if (!isset($disks[$name])) throw new DiskNotFoundException($name);

    $this->disk = $name;
    $this->path = (isset($disks[$name]['root']) ? $disks[$name]['root'] : null);

    return false;
  }

  /**
   * __isStatic
   *
   * @return bool
   */
  public static function __isStatic() : bool
  {
    $backtrace = debug_backtrace();
    return $backtrace[1]['type'] == '::';
  }

  /**
   * __getDefault
   *
   * @return string|null
   */
  public static function __getDefault()
  {
    $disks   = config('filesystems.disks');
    $default = config('filesystems.default');

    return (isset($disks[$default]['root']) ? $disks[$default]['root'] : null);
  }

  /**
   * Select disk
   *
   * @return Storage
   */
  public static function disk(string $name)
  {
    return new Storage($name);
  }

  /**
   * Get file path
   *
   * @param string $name
   * @return void
   */
  public function url(string $name)
  {
    $path = self::__isStatic() ? self::__getDefault() : $this->path;
    $file = (substr($name, 0, 1) == DIRECTORY_SEPARATOR ? substr($name, 1) : $name);
    return realpath($path . DIRECTORY_SEPARATOR . $file);
  }

  /**
   * Get file path
   *
   * @param string $name
   * @return void
   */
  public function path(string $name)
  {
    return self::url($name);
  }

  /**
   * Write the contents of a file.
   *
   * @param  string  $path
   * @param  string  $contents
   * @param  bool  $lock
   * @return int
   */
  public function put(string $name, $contents, $lock = false)
  {
    return Filesystem::put(self::url($name), $contents, $lock);
  }

  /**
   * Get file contents
   *
   * @param string $name
   * @return void
   */
  public function get(string $name)
  {
    return Filesystem::get(self::url($name));
  }

  /**
   * Append to a file.
   *
   * @param  string  $path
   * @param  string  $data
   * @return int
   */
  public function append(string $name, $data)
  {
    return Filesystem::append(self::url($name), $data, FILE_APPEND);
  }

  /**
   * Get size
   *
   * @param string $name
   * @return void
   */
  public function size(string $name)
  {
    return Filesystem::size(self::url($name));
  }

  /**
   * Download file
   *
   * @param string|null $file
   * @param string|null $name
   * @param array|null $headers
   * @return
   */
  public function download(string $file, ?string $name = null, ?array $headers = [])
  {
    return Rest::download(self::url($file), $name, $headers);
  }

  /**
   * Get storage path
   *
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }

  /**
   * Get storage disk
   *
   * @return string
   */
  public function getDisk()
  {
    return $this->disk;
  }
}
