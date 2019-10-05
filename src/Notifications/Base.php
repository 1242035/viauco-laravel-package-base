<?php
namespace Viauco\Base\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class Base extends Notification implements ShouldQueue
{
    use Queueable;
}
