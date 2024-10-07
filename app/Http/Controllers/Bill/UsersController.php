<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Models\Bill\AccessPrivileges;
use App\Models\Logs\APILogs;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function get_access_token() {
        return AccessPrivileges::where('api_environment', 'production')
            ->where('status', 1)
            ->first(['api_token','env_base_url']);
    }

    public function user_list(Request $request) {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);
        //$url = 'https://gateway.stage.bill.com/connect/v3/spend/users';
        $base_url = base64_decode($access_token->env_base_url);
        $url = "$base_url"."$request->api_path"."$request->pagination";

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
                'log_data' => 'budjet-list: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close cURL session
        curl_close($ch);
        return $response;
    }

    public function create_user_with_role(Request $request) {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);
        $url = 'https://gateway.stage.bill.com/connect/v3/spend/users';

        // Request payload
        $data = [
            'email' => $request->input('email'),
            'firstName' => $request->input('first_name'),
            'lastName' => $request->input('last_name'),
            'role' => 'MEMBER'
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
                'log_data' => 'create-user: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close the cURL session
        curl_close($ch);
        return $response;
    }

    public function get_current_user_details() {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);
        $url = 'https://gateway.stage.bill.com/connect/v3/spend/users/current';

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

    public function get_user_details($user_id) {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);
        $url = "https://gateway.stage.bill.com/connect/v3/spend/users/$user_id";

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

    public function delete_user($user_id) {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);
        $url = "https://gateway.stage.bill.com/connect/v3/spend/users/$user_id";

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apiToken: ' . $api_token,
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL request
        $response = curl_exec($ch);
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for errors
        if (curl_errno($ch)) {
            APILogs::create([
                'header_response_code' => $response_code,
                'log_data' => 'Curl error: ' . curl_error($ch),
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        } else {
            APILogs::create([
                'header_response_code' => $response_code,
                'log_data' => 'current-user-details: ' . $response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close cURL session
        curl_close($ch);
        return $response;
    }

    public function update_user(Request $request, $user_id) {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);
        $url = "https://gateway.stage.bill.com/connect/v3/spend/users/$user_id";

        // Request payload (you can include only fields that need updating)
        $data = [
            'hasDateOfBirth' => $request->input('date_of_birth')
        ];

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options for PATCH request
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apiToken: ' . $api_token,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Use PATCH instead of POST
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute cURL request and get the response
        $response = curl_exec($ch);

        // Get Header Response Code
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for errors
        if (curl_errno($ch)) {
            APILogs::create([
                'header_response_code' => $response_code,
                'log_data' => 'Curl error: ' . curl_error($ch),
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        } else {
            APILogs::create([
                'header_response_code' => $response_code,
                'log_data' => 'update-user: ' . $response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close the cURL session
        curl_close($ch);
        return $response;
    }
}
