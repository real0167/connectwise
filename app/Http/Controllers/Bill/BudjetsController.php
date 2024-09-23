<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Models\Bill\AccessPrivileges;
use App\Models\Logs\APILogs;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BudjetsController extends Controller
{
    public function get_access_token() {
        return AccessPrivileges::where('api_environment', 'sandbox')
            ->where('status', 1)
            ->first('api_token');
    }

    public function budjet_list() {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);
        $url = 'https://gateway.stage.bill.com/connect/v3/spend/users';

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apiToken: ' . $api_token,     // Add the API token in the header
            'Accept: application/json',    // Specify the content type you are expecting
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response instead of outputting it

        // Execute cURL request
        $response = curl_exec($ch);

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
                'log_data' => 'user-list: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close cURL session
        curl_close($ch);
        return $response;
    }

    public function create_budjet() {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);

        $user_id = "VXNlcjoxMjgzODU4";
        $url = 'https://gateway.stage.bill.com/connect/v3/spend/budgets';

        // Request payload
        $data = [
            'name' => 'Happy Music Supplies monthly spending budget',
            'owners' => [$user_id],
            'recurringInterval' => 'MONTHLY',
            'limit' => 100,
            'recurringLimit' => 200
        ];

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apiToken: ' . $api_token,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute cURL request and get the response
        $response = curl_exec($ch);

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
                'log_data' => 'create-budjet: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close cURL session
        curl_close($ch);
        return $response;
    }

    public function add_user_to_budjet() {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);
        $budget_id = "QnVkZ2V0OjU3OTUzNQ==";
        $user_id = "VXNlcjoxMjgzODU4";
        $url = "https://gateway.stage.bill.com/connect/v3/spend/budgets/$budget_id/members/$user_id";

        // Request payload
        $data = [
            'limit' => 50,
            'recurringLimit' => 100
        ];

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apiToken: ' . $api_token,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');  // Use PUT request
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute cURL request and get the response
        $response = curl_exec($ch);

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
                'log_data' => 'add-user-to-budjet: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close cURL session
        curl_close($ch);
        return $response;
    }
}
