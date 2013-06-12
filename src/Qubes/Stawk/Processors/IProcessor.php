<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Stawk\Processors;

use Qubes\Stawk\Events\StawkEvent;

interface IProcessor
{
  public function process(StawkEvent $event);
}
