<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Stawk\Helpers;

use Cubex\FileSystem\FileSystem;

class Uuid
{
  /**
   * Create a unique ID based on an event type and time
   *
   * @param      $eventType string
   * @param null $time      float microtime(true)
   *
   * @return string Unique ID
   */
  public static function namedTimeUuid($eventType, $time = null)
  {
    if($time === null)
    {
      $time = microtime(true);
    }
    $uuid = $eventType . '-' . $time . '-';
    $uuid .= FileSystem::readRandomCharacters(20);
    return $uuid;
  }

  /**
   * Retrieve the event microtime from a unique ID
   *
   * @param $uuid string
   *
   * @return float Microtime of event
   */
  public static function timeFromUuid($uuid)
  {
    list(, $time,) = explode('-', $uuid, 3);
    return $time;
  }

  /**
   * Retrieve the event type from a unique ID
   *
   * @param $uuid string
   *
   * @return string type of the event
   */
  public static function eventTypeFromUuid($uuid)
  {
    list($type,) = explode('-', $uuid, 2);
    return $type;
  }
}
