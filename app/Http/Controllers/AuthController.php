<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bill\AccessPrivileges;
use Illuminate\Http\Request;
use App\Models\Logs\APILogs;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
//use App\Http\Controllers\Web\HttpRequest;


class AuthController extends Controller
{
    public function get_access_token() {
        return AccessPrivileges::where('api_environment', 'production')
            ->where('source_platform', 'connect-wise')
            ->where('status', 1)
            ->first();
    }
    public function userAuthentication(Request $response) {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);

        $company = $access_token->company_name;
        $publicKey = base64_decode($access_token->public_key);
        $privateKey = base64_decode($access_token->private_key);
        $authString = base64_encode("$company+$publicKey:$privateKey");
        $client_id = base64_decode($access_token->client_id);

        // Set the API endpoint you want to call
        //$apiEndpoint = 'https://api-na.myconnectwise.net/v4_6_release/apis/3.0/your_endpoint';
        $apiEndpoint = 'https://na.myconnectwise.net/v4_6_release/apis/3.0/finance/billingCycles/info';
                       //'https://api-na.myconnectwise.net/v4_6_release/apis/3.0/finance/billingCycles/info';

        // Initialize cURL
        $ch = curl_init($apiEndpoint);

        // Set the HTTP headers
        $headers = [
            "Authorization: Basic $authString",
            "clientid: $client_id",
        ];

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute the request
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
                'log_data' => 'Curl success: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close the cURL session
        curl_close($ch);
        return $response;
    }

    public function billableOptions(Request $response) {
        $company = 'cipesol'; //compnay_name = 'Cipe_Solutions'; 4fecc1bf-f944-45aa-b623-60ebbf692e37  //cipesol
        //$company = '2bcabcd3-49f5-4a67-901e-4d14d6f4ab06';
        $publicKey = 'mfvuxlMqU7XgjdqZ';
        $privateKey = 'Z2Om5Ly8qu5QkW94';
        $authString = base64_encode("$company+$publicKey:$privateKey");

        // Set the API endpoint you want to call
        //$apiEndpoint = 'https://na.myconnectwise.net/v4_6_release/apis/3.0/finance/billingCycles';
        $apiEndpoint = 'https://na.myconnectwise.net/v4_6_release/apis/3.0/system/BillableOptions/info';

        // Initialize cURL
        $ch = curl_init($apiEndpoint);

        // Set the HTTP headers
        $headers = [
            "Authorization: Basic $authString",
            'clientid: 2bcabcd3-49f5-4a67-901e-4d14d6f4ab06',
        ];

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute the request
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
                'log_data' => 'Curl success: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close the cURL session
        curl_close($ch);
        return $response;
    }

    public function common_api_old(Request $response) {
        $company = 'cipesol'; //compnay_name = 'Cipe_Solutions'; 4fecc1bf-f944-45aa-b623-60ebbf692e37  //cipesol
        //$company = '2bcabcd3-49f5-4a67-901e-4d14d6f4ab06';
        $publicKey = 'mfvuxlMqU7XgjdqZ';
        $privateKey = 'Z2Om5Ly8qu5QkW94';
        $authString = base64_encode("$company+$publicKey:$privateKey");

        // Set the API endpoint you want to call
        //$apiEndpoint = 'https://na.myconnectwise.net/v4_6_release/apis/3.0/company/companies/info';
        $apiEndpoint = 'https://api-na.myconnectwise.net/v4_6_release/apis/3.0//company/contacts/182';

        // Initialize cURL
        $ch = curl_init($apiEndpoint);

        // Set the HTTP headers
        $headers = [
            "Authorization: Basic $authString",
            'clientid: 2bcabcd3-49f5-4a67-901e-4d14d6f4ab06',
        ];

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute the request
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
                'log_data' => 'Curl success: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close the cURL session
        curl_close($ch);
        return $response;
//        return [
//            "response_code" => 200,
//            "total_count" => count(json_decode($response)),
//            "data" => $response
//        ];
    }

    public function common_api(Request $response) {
        $access_token = $this->get_access_token();
        $api_token = base64_decode($access_token->api_token);

        $company = $access_token->company_name;
        $publicKey = base64_decode($access_token->public_key);
        $privateKey = base64_decode($access_token->private_key);
        $authString = base64_encode("$company+$publicKey:$privateKey");
        $client_id = base64_decode($access_token->client_id);

        // Set the API endpoint you want to call
        // $apiEndpoint = 'https://na.myconnectwise.net/v4_6_release/apis/3.0/company/companies/info';
        //$apiHost = "https://api-na.myconnectwise.net/v4_6_release/apis/3.0";
        $apiHost = "https://na.myconnectwise.net/v4_6_release/apis/3.0";
        $apiPath = $response->api_path;
        $apiEndpoint = $apiHost.$apiPath;

        // Initialize cURL
        $ch = curl_init($apiEndpoint);

        // Set the HTTP headers
        $headers = [
            "Authorization: Basic $authString",
            "clientid: $client_id",
        ];

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute the request
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
                'log_data' => 'Curl success: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close the cURL session
        curl_close($ch);
        return $response;
        /*return [
            "response_code" => 200,
            "total_count" => count(json_decode($response)),
            "data" => $response
        ];*/
    }

    public function agreements(Request $request) {
        $company = 'cipesol'; //compnay_name = 'Cipe_Solutions'; 4fecc1bf-f944-45aa-b623-60ebbf692e37  //cipesol
        //$company = '2bcabcd3-49f5-4a67-901e-4d14d6f4ab06';
        $publicKey = 'mfvuxlMqU7XgjdqZ';
        $privateKey = 'Z2Om5Ly8qu5QkW94';
        $authString = base64_encode("$company+$publicKey:$privateKey");

        // Set the API endpoint you want to call
        //$apiEndpoint = 'https://na.myconnectwise.net/v4_6_release/apis/3.0/system/BillableOptions/info';
        $apiEndpoint = 'https://na.myconnectwise.net/v4_6_release/apis/3.0/finance/agreements';

        // Initialize cURL
        $ch = curl_init($apiEndpoint);

        // Set the HTTP headers
        $headers = [
            "Authorization: Basic $authString",
            'clientid: 2bcabcd3-49f5-4a67-901e-4d14d6f4ab06',
        ];

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute the request
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
                'log_data' => 'Curl success: ' .$response,
                'created_at' => Carbon::parse(now())->toDateTimeString()
            ]);
        }

        // Close the cURL session
        curl_close($ch);
        return $response;
    }
}
