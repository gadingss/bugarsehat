<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Membership;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendMembershipReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membership:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp reminders to members whose membership is about to expire (3 days before).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Checking for memberships expiring in 3 days...");

        // Get memberships that expire exactly 3 days from today
        $targetDate = Carbon::today()->addDays(3)->toDateString();
        
        $expiringMemberships = Membership::with(['user', 'package'])
            ->where('status', 'active')
            ->whereDate('end_date', $targetDate)
            ->get();

        if ($expiringMemberships->isEmpty()) {
            $this->info("No memberships expiring on {$targetDate}.");
            return;
        }

        $sentCount = 0;
        foreach ($expiringMemberships as $membership) {
            $user = $membership->user;
            $package = $membership->package;
            
            if (!$user || empty($user->phone)) {
                $this->warn("User ID {$membership->user_id} has no valid phone number. Skipping.");
                continue;
            }

            // Format message
            $message = "Halo {$user->name},\n\n";
            $message .= "Kami dari *Bugar Sehat* ingin mengingatkan bahwa masa aktif membership Anda untuk paket *{$package->name}* akan segera berakhir dalam 3 hari lagi (pada tanggal *" . Carbon::parse($membership->end_date)->format('d-m-Y') . "*).\n\n";
            $message .= "Jangan lewatkan rutinitas olahraga Anda! Silakan hubungi staff kami atau datang ke Bugar Sehat untuk melakukan perpanjangan.\n\n";
            $message .= "Terima kasih dan tetap fit!\n- Tim Bugar Sehat";

            $this->info("Sending reminder to {$user->name} ({$user->phone})...");
            
            $success = FonnteService::sendMessage($user->phone, $message);
            
            if ($success) {
                $sentCount++;
            }
        }

        $this->info("Done! Successfully sent {$sentCount} reminders.");
        Log::info("Membership Reminder Command executed. Sent {$sentCount} WhatsApp messages.");
    }
}
