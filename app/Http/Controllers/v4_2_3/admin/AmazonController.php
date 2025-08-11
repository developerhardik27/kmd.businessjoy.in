<?php

namespace App\Http\Controllers\v4_2_3\admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class AmazonController extends Controller
{
    public function amazonauthorize(Request $request)
    {
        $clientId = "amzn1.application-oa2-client.1770731595644e70bce8cabdb297cb16";
        $redirectUri = urlencode(route('amazon.callback'));
        $state = "ABCD"; // Can be random string or user id to validate response

        $url = "https://sellercentral.amazon.com/apps/authorize/consent?application_id={$clientId}&state={$state}&redirect_uri={$redirectUri}";

        return redirect()->away($url);
    }

    public function amazoncallback(Request $request)
    {
        $code = $request->get('spapi_oauth_code');
        $state = $request->get('state');

        if ($state != auth()->id()) {
            abort(403, "Invalid state");
        }

        $response = Http::asForm()->post('https://api.amazon.com/auth/o2/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => config('amazon.client_id'),
            'client_secret' => config('amazon.client_secret'),
            'redirect_uri' => config('amazon.oauth_redirect_uri'),
        ]);

        $data = $response->json();

        if (!isset($data['refresh_token'])) {
            return redirect('/')->with('error', 'Failed to get refresh token from Amazon.');
        }

        session()->put('refresh_token', $data['refresh_token']);
        
        return redirect('/')->with('success', 'Amazon account connected!');
    }
}
