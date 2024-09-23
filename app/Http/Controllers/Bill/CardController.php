<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Models\Bill\AccessPrivileges;
use App\Models\Logs\APILogs;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function get_access_token() {
        return AccessPrivileges::where('api_environment', 'sandbox')
            ->where('status', 1)
            ->first('api_token');
    }

    public function card_list() {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);
        $url = 'https://gateway.stage.bill.com/connect/v3/spend/cards';

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apiToken: ' . $api_token,
            'Accept: application/json',
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
                'log_data' => 'card-list: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close cURL session
        curl_close($ch);
        return $response;
    }

    public function create_virtual_card() {
        // API endpoint
        $url = 'https://gateway.stage.bill.com/connect/v3/spend/cards';

        // API token
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);

        // Data to be sent
        $data = [
            'budgetId' => 'QnVkZ2V0OjU3OTUzNQ==',
            'limit' => '10',
            'name' => 'Happy Music Supplies card for Clark Spenderson',
            'userId' => 'VXNlcjoxMjgzODU4'
        ];

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apiToken: ' . $api_token,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute the request and store the response
        $response = curl_exec($ch);

        //Get Header Response Code
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
                'log_data' => 'create-card: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close the cURL session
        curl_close($ch);
        return $response;
    }

    public function get_card_details($card_id) {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);
        $url = "https://gateway.stage.bill.com/connect/v3/spend/cards/$card_id";

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
                'log_data' => 'current-user-details: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close cURL session
        curl_close($ch);
        return $response;
    }

    /*
     * primary access number
     * json
     */
    public function get_pan_jwt($card_id) {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);

        $url = "https://gateway.stage.bill.com/connect/v3/spend/cards/$card_id/pan-jwt";

        // Initialize cURL
        $ch = curl_init($url);

        // Set the cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apiToken: $api_token",
            'Accept: application/json'
        ]);

        // Execute the request
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
                'log_data' => 'get-pan-jwt: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close cURL session
        curl_close($ch);

        return $this->get_jwt_to_pan($response->token);
        //return $response;
    }

    //eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjoiVTFlWm5WcU01L1ROQlRyYzIzQUJCMFU5Zzd1M3FrY1R6L3BnMWNwSkQwWXR0TTFUbU5lRFVGRnBSK1RZSDFHZmNzYkd0VE1wVEMxd0JURG13Y1Z2eUZYQWdXK0lXVm8xRHNJL05jN0owSS9yMDU0SGdYQjVvbHlCY29MZmk2bG5oUnlOaHRsMm1oVGJSMEZEeGtvVHN4bGpHYkQrT0ZHcWpER2dsRTcweEE9PSIsImV4cCI6MTcyNzA3MTMzNX0.7_BR9V6_D5Z3qChANeN_tP3y-hkWhQBUu4bzk4pn_oQ
    public function get_jwt_to_pan() {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);
        $url = 'https://app-dev-sandbox.divvy.co/de/rest/pan';

        // Prepare the data payload
        $data = json_encode([
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjoiVTFlWm5WcU01L1ROQlRyYzIzQUJCMFU5Zzd1M3FrY1R6L3BnMWNwSkQwWXR0TTFUbU5lRFVGRnBSK1RZSDFHZmNzYkd0VE1wVEMxd0JURG13Y1Z2eUZYQWdXK0lXVm8xRHNJL05jN0owSS9yMDU0SGdYQjVvbHlCY29MZmk2bG5oUnlOaHRsMm1oVGJSMEZEeGtvVHN4bGpHYkQrT0ZHcWpER2dsRTcweEE9PSIsImV4cCI6MTcyNzA3MTMzNX0.7_BR9V6_D5Z3qChANeN_tP3y-hkWhQBUu4bzk4pn_oQ'
        ]);

        // Initialize cURL
        $ch = curl_init($url);

        // Set the cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apiToken: ' . $api_token,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Attach the JSON data

        // Execute the request
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
                'log_data' => 'get-jwt-to-pan: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close cURL session
        curl_close($ch);

        return $response;
    }
}
