<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Stawk\Consumers;

use Cubex\Container\Container;
use Cubex\Data\Validator\Validator;
use Cubex\Log\Log;
use Cubex\Queue\IQueue;
use Cubex\Queue\IQueueConsumer;
use Cubex\Queue\IQueueProvider;
use Cubex\Queue\StdQueue;
use Cubex\Text\TextTable;
use Qubes\Stawk\Enums\HookType;
use Qubes\Stawk\Events\StawkEvent;
use Qubes\Stawk\Helpers\Uuid;
use Qubes\Stawk\Mappers\Hooks;
use Qubes\Stawk\Mappers\QueueHook;
use Qubes\Stawk\Mappers\RawEvent;
use Qubes\Stawk\Mappers\RawEventHistory;
use Qubes\Stawk\Mappers\RawEventHistoryCounter;
use Qubes\Stawk\Processors\IProcessor;
use Qubes\Stawk\Queues\EventValidator;

class RawConsumer implements IQueueConsumer
{
  /**
   * @param $queue
   * @param $data
   *
   * @return bool
   */
  public function process(IQueue $queue, $data)
  {
    $validator = new EventValidator();
    if(!$validator->isValid($data))
    {
      Log::error(
        "Invalid Event Received: " . implode(', ', $validator->errorMessages())
      );
    }
    else
    {
      $event = $this->buildStawkEvent((array)$data);
      $uuid  = Uuid::namedTimeUuid($event->name(), $event->eventTime());
      $event->setUuid($uuid);

      Log::debug("Event is valid, assuming uuid '" . $uuid . "'");

      $this->storeRawEvent($uuid, $event);

      $hooks = Hooks::collectionOn(new RawEvent($event->name()))->get();
      if($hooks->count() > 0)
      {
        /**
         * @var $hooks Hooks[]
         */
        foreach($hooks as $hook)
        {
          $this->hook($hook->queueHookId, $event);
        }
      }
    }

    return true;
  }

  public function hook($queueHookId, StawkEvent $event)
  {
    $queueHook = new QueueHook($queueHookId);
    if($queueHook->exists())
    {
      Log::debug("Hooking to " . $queueHook->name);
      switch($queueHook->hookType)
      {
        case HookType::QUEUE:
          $queue = Container::servicemanager()->get($queueHook->queueService);
          if($queue instanceof IQueueProvider)
          {
            $pushQueue = new StdQueue($queueHook->queueName);
            $queue->push($pushQueue, $event);
            Log::debug("Pushed event to $queueHook->queueName");
          }
          else
          {
            Log::error(
              "'$queueHook->queueService' is not a valid IQueueProvider"
            );
          }
          break;
        case HookType::PROCESS:
          $process = $queueHook->processClass;
          if($process !== null && class_exists($process))
          {
            $processor = new $process;
            if($processor instanceof IProcessor)
            {
              $processor->process($event);
            }
            else
            {
              Log::error("'$process' is not a valid IProcessor");
            }
          }
          else
          {
            Log::error("Processor '$process' does not exist");
          }
          break;
        default:
          Log::error(
            "$queueHook->name has an invalid hook type " .
            "'" . $queueHook->hookType . "'"
          );
          return false;
      }
      Log::debug("Hook Processed");
      return true;
    }
    else
    {
      Log::warning("Queue $queueHookId does not exist");
    }
    return false;
  }

  public function buildStawkEvent(array $data, $uuid = null)
  {
    $event = new StawkEvent(
      $data['name'], (array)$data['data'], $data['source']
    );
    $event->setEventTime($data['time']);
    if($uuid !== null)
    {
      $event->setUuid($uuid);
    }
    return $event;
  }

  /**
   * Store the stawk event in long term storage
   *
   * @param            $uuid
   * @param StawkEvent $event
   */
  public function storeRawEvent($uuid, StawkEvent $event)
  {
    $storage = new RawEventHistory();
    Log::debug(
      "Writing raw event to row key " . $storage->generateRowKey($event)
    );
    $storage->setId($storage->generateRowKey($event));
    $storage->setData($uuid, json_encode($event));
    $storage->saveChanges();
    RawEventHistoryCounter::cf()->increment(
      date("Ym-") . strtolower($event->name()),
      date("YmdHi")
    );
  }

  /**
   * Seconds to wait before re-attempting, false to exit
   *
   * @param int $waits amount of times script has waited
   *
   * @return mixed
   */
  public function waitTime($waits = 0)
  {
    return 1;
  }

  public function shutdown()
  {
    return true;
  }
}
