<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateAppointmentStatus extends Command
{
    protected $signature = 'appointments:update-status';
    protected $description = 'Update status of past appointments to expired';

    public function handle()
    {
        $now = Carbon::now();

        // Combine date and time to check expired ones
        $expiredAppointments = Appointment::where('appointment_status', 'pending') // or your status key
            ->whereRaw("STR_TO_DATE(CONCAT(appointment_date, ' ', appointment_time), '%Y-%m-%d %H:%i') < ?", [$now])
            ->update(['appointment_status' => 'expired']); // use your actual enum or constant here

        Log::info("Updated $expiredAppointments expired appointments at " . now());
    }
}
