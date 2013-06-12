<?php
/**
 * @author  brooke.bryan
 */

namespace Qubes\Stawk\Enums;

use Cubex\Type\Enum;

class HookType extends Enum
{
  const __default = 'queue';
  const QUEUE     = 'queue';
  const PROCESS   = 'process';
}
