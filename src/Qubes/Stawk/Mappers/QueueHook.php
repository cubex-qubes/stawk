<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Stawk\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Qubes\Stawk\Enums\HookType;

class QueueHook extends RecordMapper
{
  /**
   * description of the queue hook
   */
  public $description;
  /**
   * hook name
   */
  public $name;
  /**
   * @enumclass \Qubes\Stawk\Enums\HookType
   */
  public $hookType = HookType::QUEUE;
  /**
   * queue service name to push events to
   */
  public $queueService;
  /**
   * name of the queue to push events to
   */
  public $queueName;
  /**
   * class to process event through
   */
  public $processClass;
}
