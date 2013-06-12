<?php
/**
 * @author  brooke.bryan
 *
 * Events with UUIDs containing event time and type
 *
 */

namespace Qubes\Stawk\Events;

use Cubex\Events\StdEvent;
use Qubes\Stawk\Helpers\Uuid;

class StawkEvent extends StdEvent
{
  protected $_uuid;

  /**
   * @param $time float microtime(true)
   *
   * @return $this
   */
  public function setEventTime($time)
  {
    $this->_eventTime = $time;
    return $this;
  }

  /**
   * @return string Unique Event ID
   */
  public function getUuid()
  {
    return $this->_uuid;
  }

  /**
   * @param $uuid string Unique Event ID
   *
   * @return $this
   */
  public function setUuid($uuid)
  {
    $this->_uuid = $uuid;
    return $this;
  }

  public function jsonSerialize()
  {
    return $this->_data;
  }

  /**
   * @param $data string JSON representation of event
   *
   * @return StawkEvent
   */
  public static function rebuildFromQueueData($data)
  {
    if(is_scalar($data))
    {
      $data = json_decode($data);
    }
    return self::rebuildEvent($data->uuid, $data->data);
  }

  /**
   * @param $uuid string Unique Event ID
   * @param $data mixed Event Data
   *
   * @return StawkEvent
   */
  public static function rebuildEvent($uuid, $data)
  {
    $name  = Uuid::eventTypeFromUuid($uuid);
    $time  = Uuid::timeFromUuid($uuid);
    $event = new self($name, (array)$data);
    $event->setEventTime($time);
    $event->setUuid($uuid);
    return $event;
  }
}
