<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Stawk\Cli;

use Cubex\Cli\CliCommand;
use Cubex\Cli\Shell;
use Cubex\Events\EventManager;
use Cubex\Events\IEvent;
use Cubex\Facade\Queue;
use Cubex\Figlet\Figlet;
use Cubex\Log\Log;
use Cubex\Queue\StdQueue;
use Qubes\Stawk\Consumers\RawConsumer;

/**
 * Read source queue and distribute to hooked queues
 */
class Distribute extends CliCommand
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

  protected $_echoLevel = 'debug';

  /**
   * @return int exit code
   */
  public function execute()
  {
    EventManager::listen(
      EventManager::CUBEX_QUERY,
      function (IEvent $e)
      {
        Log::debug($e->getStr("query"));
      }
    );

    //Stawk Distribute
    echo Shell::colourText(
      (new Figlet("small"))->render("StawkD"),
      Shell::COLOUR_FOREGROUND_GREEN
    );
    echo "\n";

    Log::info("Starting Distribute");

    Log::debug("Setting Default Queue Provider to " . $this->queueService);
    Queue::setDefaultQueueProvider($this->queueService);

    Log::info("Starting to consume queue " . $this->queueName);
    Queue::consume(new StdQueue($this->queueName), new RawConsumer());

    Log::info("Exiting Distribute");
  }
}
