<?php

namespace App\Jobs;

use App\Models\DaftarWS_Model;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;



class CheckExpiredWorksheetsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $now = Carbon::now();

        // Find worksheets that are expired
        $expiredWorksheets = DaftarWS_Model::where('status_ws', 'OPEN')
            ->whereNotNull('expired_at_ws')
            ->where('expired_at_ws', '<', $now)
            ->get();

        foreach ($expiredWorksheets as $worksheet) {
            // Update the status to LOCKED or any other status you prefer
            $worksheet->status_ws = 'CLOSED';
            $worksheet->closed_at_ws = Carbon::now()->format('Y-m-d H:i:s');
            $worksheet->save();

            // Optionally log or notify about the locked worksheets
            Log::info("Worksheet ID {$worksheet->id_ws} has been locked due to expiration.");
        }
    }
}
