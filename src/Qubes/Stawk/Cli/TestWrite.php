<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Stawk\Cli;

use Cubex\Cli\CliCommand;
use Cubex\Events\StdEvent;
use Cubex\Facade\Queue;
use Cubex\Log\Log;
use Cubex\Queue\StdQueue;

class TestWrite extends CliCommand
{
  /**
   * Queue Provider Service to read raw events from
   *
   * @valuerequired
   */
  public $queueService = 'queue';

  /**
   * Queue Name to pull raw events from
   *
   * @valuerequired
   */
  public $queueName = 'stawkr';

  /**
   * Message to send
   *
   * @valuerequired
   */
  public $message = 'This is a test message';

  protected $_echoLevel = 'debug';

  public function execute()
  {
    Log::info("Starting Distribute");

    Log::debug("Setting Default Queue Provider to " . $this->queueService);

    Queue::setDefaultQueueProvider($this->queueService);

    Log::info("Pushing test to queue " . $this->queueName);

    $push = new StdEvent("stawk.test", ["message" => $this->message], $this);

    Queue::push(new StdQueue($this->queueName), $push);

    Log::info("Pushed");
  }
}
