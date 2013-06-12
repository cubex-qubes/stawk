<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Stawk\Mappers;

use Cubex\Cassandra\CassandraMapper;

class RawEventHistoryCounter extends CassandraMapper
{
  protected $_cassandraConnection = 'cassandra_stawk';
}
