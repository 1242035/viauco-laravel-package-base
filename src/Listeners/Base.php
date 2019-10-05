<?php
namespace Viauco\Base\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notifiable;

class Base implements ShouldQueue
{
    use InteractsWithQueue, Notifiable;
}
