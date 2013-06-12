<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Stawk\Mappers;

use Cubex\Mapper\Database\PivotMapper;

class Hooks extends PivotMapper
{
  /**
   * @datatype varchar
   * @length   50
   */
  public $rawEventId;

  public $queueHookId;

  protected function _configure()
  {
    $this->pivotOn(new RawEvent(), new QueueHook());
  }
}
