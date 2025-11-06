<?php

namespace App\Http\Controllers\admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AmazonConnection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class AmazonController extends Controller
{
    public function amazonauthorize(Request $request)
    {
        $clientId = env('AMAZON_CLIENT_ID');
        $redirectUri = urlencode(route('amazon.callback'));

        // Get admin user id from your custom guard
        $adminId = auth()->guard('admin')->id();

        if (!$adminId) {
            abort(403, "You must be logged in as admin to connect Amazon.");
        }

        // Encrypt user id to use as state
        $state = encrypt($adminId);

        $url = "https://sellercentral.amazon.com/apps/authorize/consent?application_id={$clientId}&state={$state}&redirect_uri={$redirectUri}?version=beta";

        return redirect()->away($url);
    }
 
    public function amazoncallback(Request $request)
    {
        $code = $request->get('spapi_oauth_code');
        $state = $request->get('state');

        try {
            $adminId = decrypt($state);
        } catch (\Exception $e) {
            abort(403, "Invalid state or tampered request");
        }

        // Exchange code for tokens
        $response = Http::asForm()->post('https://api.amazon.com/auth/o2/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => env('AMAZON_CLIENT_ID'),
            'client_secret' => env('AMAZON_CLIENT_SECRET'),
            'redirect_uri' => env('AMAZON_OAUTH_REDIRECT_URI'),
        ]);

        $data = $response->json();

        if (!isset($data['refresh_token'])) {
            abort(400, "Failed to get refresh token from Amazon.");
        }

        // Use refresh token to get temporary access token
        $accessTokenResponse = Http::asForm()->post('https://api.amazon.com/auth/o2/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $data['refresh_token'],
            'client_id' => env('AMAZON_CLIENT_ID'),
            'client_secret' => env('AMAZON_CLIENT_SECRET'),
        ]);

        $accessTokenData = $accessTokenResponse->json();
        $accessToken = $accessTokenData['access_token'] ?? null;

        // Call Marketplace Participations to get selling partner ID
        $marketplacesResponse = Http::withHeaders([
            'x-amz-access-token' => $accessToken,
            'Content-Type' => 'application/json',
        ])->get('https://sellingpartnerapi-na.amazon.com/sellers/v1/marketplaceParticipations');

        $marketplaces = $marketplacesResponse->json();
        $sellingPartnerId = $marketplaces['payload'][0]['sellerId'] ?? null;

        // Save tokens and selling_partner_id
        AmazonConnection::updateOrCreate(
            ['user_id' => $adminId],
            [
                'selling_partner_id' => $sellingPartnerId,
                'refresh_token' => $data['refresh_token'],
                'access_token' => $accessToken,
                'access_token_expires_at' => now()->addSeconds($accessTokenData['expires_in'] ?? 3600),
            ]
        );

        return redirect()->route('admin.dashboard')->with('success', 'Amazon account connected successfully!');
    }
}
