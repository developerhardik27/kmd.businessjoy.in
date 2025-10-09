<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AmazonConnection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class RefreshAmazonTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amazon:refresh-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh and update Amazon SP-API access tokens for all connected users.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connections = AmazonConnection::all();
        $this->info('Starting Amazon token refresh for ' . $connections->count() . ' accounts...');

        foreach ($connections as $connection) {
            try {
                if (!$connection->refresh_token) {
                    $this->warn("Skipping user {$connection->user_id} — no refresh token found.");
                    continue;
                }

                // Only refresh if expired or missing
                if ($connection->access_token && now()->lt($connection->access_token_expires_at)) {
                    $this->line("Token still valid for user {$connection->user_id}, skipping refresh.");
                    continue;
                }

                $response = Http::asForm()->post('https://api.amazon.com/auth/o2/token', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $connection->refresh_token,
                    'client_id' => env('AMAZON_CLIENT_ID'),
                    'client_secret' => env('AMAZON_CLIENT_SECRET'),
                ]);

                if ($response->failed()) {
                    $this->error("Failed to refresh token for user {$connection->user_id}");
                    Log::error("Amazon token refresh failed for user {$connection->user_id}: " . $response->body());
                    continue;
                }

                $data = $response->json();

                if (!isset($data['access_token'])) {
                    $this->error("Missing access_token in response for user {$connection->user_id}");
                    Log::error("Amazon response missing access_token for user {$connection->user_id}: " . json_encode($data));
                    continue;
                }

                $connection->update([
                    'access_token' => $data['access_token'],
                    'access_token_expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
                ]);

                $this->info("Token refreshed for user {$connection->user_id}");
            } catch (\Exception $e) {
                Log::error("Amazon token refresh error for user {$connection->user_id}: " . $e->getMessage());
                $this->error("Exception for user {$connection->user_id}: " . $e->getMessage());
            }
        }

        $this->info('🎉 Amazon token refresh completed.');
        return Command::SUCCESS;
    }
}
