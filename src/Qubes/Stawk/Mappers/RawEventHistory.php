<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Stawk\Mappers;

use Cubex\Cassandra\CassandraMapper;
use Cubex\Events\IEvent;

class RawEventHistory extends CassandraMapper
{
  protected $_cassandraConnection = 'cassandra_stawk';

  public function generateRowKey(IEvent $event)
  {
    return date("YmdHi") . ':' . strtolower($event->name());
  }
}
