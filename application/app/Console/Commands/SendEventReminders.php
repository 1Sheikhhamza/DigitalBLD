<?php

namespace App\Console\Commands;

use App\Mail\EventReminderMail;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for upcoming events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $checkTime = $now->copy()->addMinutes(60); // 60 minutes ahead

        $events = Event::where('reminder_sent', false)
            ->whereDate('start_date', $checkTime->toDateString())
            ->whereTime('start_time', '<=', $checkTime->toTimeString())
            ->get();

        foreach ($events as $event) {
            Mail::to($event->user->email)->send(new EventReminderMail($event));
            $event->reminder_sent = true;
            $event->save();
        }

        $this->info('Reminders sent successfully!');
    }
}
