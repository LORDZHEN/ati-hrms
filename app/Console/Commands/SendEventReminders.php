<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\User;
use App\Notifications\EventReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'events:send-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Send reminders for upcoming events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for upcoming events to send reminders...');

        $now = now();

        // Get all active users
        $users = User::all();

        // Events happening in 1 hour
        $eventsInOneHour = Event::where('is_active', true)
            ->whereDate('event_date', $now->format('Y-m-d'))
            ->get()
            ->filter(function ($event) use ($now) {
                $eventDateTime = $event->event_date->setTimeFromTimeString($event->event_time->format('H:i:s'));
                $diffInMinutes = $now->diffInMinutes($eventDateTime, false);
                return $diffInMinutes >= 55 && $diffInMinutes <= 65; // 1 hour window (±5 minutes)
            });

        foreach ($eventsInOneHour as $event) {
            $this->info("Sending 1-hour reminder for: {$event->title}");
            Notification::send($users, new EventReminder($event, 'one_hour'));
        }

        // Events happening today (sent at 8 AM)
        if ($now->hour === 8 && $now->minute < 10) {
            $eventsToday = Event::where('is_active', true)
                ->whereDate('event_date', $now->format('Y-m-d'))
                ->get();

            foreach ($eventsToday as $event) {
                $this->info("Sending today reminder for: {$event->title}");
                Notification::send($users, new EventReminder($event, 'today'));
            }
        }

        // Events happening tomorrow (sent at 5 PM the day before)
        if ($now->hour === 17 && $now->minute < 10) {
            $eventsTomorrow = Event::where('is_active', true)
                ->whereDate('event_date', $now->addDay()->format('Y-m-d'))
                ->get();

            foreach ($eventsTomorrow as $event) {
                $this->info("Sending tomorrow reminder for: {$event->title}");
                Notification::send($users, new EventReminder($event, 'tomorrow'));
            }
        }

        $this->info('Finished sending event reminders.');
        $this->info("One hour reminders: {$eventsInOneHour->count()}");

        return Command::SUCCESS;
    }
}
