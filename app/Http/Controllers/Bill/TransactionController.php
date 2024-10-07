<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Models\Bill\AccessPrivileges;
use App\Models\Logs\APILogs;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function get_access_token() {
        return AccessPrivileges::where('api_environment', 'production')
            ->where('status', 1)
            ->first(['api_token','env_base_url']);
    }

    public function get_transaction_list(Request $request) {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);
        $base_url = base64_decode($access_token->env_base_url);
        $url = "$base_url"."$request->api_path"."$request->pagination"; //?nextPage=YXJyYXljb25uZWN0aW9uOjE5

        // Initialize cURL session
        $ch = curl_init($url);

        //~ Set cURL options
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apiToken: ' . $api_token,
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response instead of outputting it

        //` Execute cURL request
        $response = curl_exec($ch);

        //~ Get Http header response
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for errors
        if(curl_errno($ch)) {
            APILogs::create([
                'header_response_code' => $response_code,
                'log_data' => 'Curl error: ' . curl_error($ch),
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        } else {
            APILogs::create([
                'header_response_code' => $response_code,
                'log_data' => 'transaction-list: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close cURL session
        curl_close($ch);
        return $response;
    }
}
