<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Models\Bill\AccessPrivileges;
use App\Models\Bill\Card;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CardControllerV2 extends Controller
{
    public function get_access_token() {
        return AccessPrivileges::where('api_environment', 'production')
            ->where('status', 1)
            ->first(['api_token', 'env_base_url']);
    }

    private function make_api_request($url, $api_token) {
        try {
            // Path to the 'cacert.pem' file
            $certPath = Storage::path('cacert.pem');  // Ensure 'cacert.pem' is in storage path
    
            // Make the API request with the specified certificate
            $response = Http::withHeaders([
                'apiToken' => $api_token,
                'Accept' => 'application/json',
            ])->withOptions([
                'verify' => $certPath, // Add this option to specify the CA bundle
            ])->get($url);
    
            if ($response->failed()) {
                Log::error('API request failed', ['url' => $url, 'status' => $response->status(), 'response' => $response->body()]);
                throw new \Exception('API request failed with status: ' . $response->status());
            }
    
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error during API request', ['message' => $e->getMessage()]);
            throw new \Exception('Failed to make API request: ' . $e->getMessage());
        }
    }

    public function get_card_data($request, $pagination) {
        $access_token = $this->get_access_token();

        // Decode tokens
        $api_token = base64_decode($access_token->api_token);
        $base_url = base64_decode($access_token->env_base_url);

        // Build the API URL
        $pagination_param = $pagination ? "?nextPage=$pagination" : '';
        $url = rtrim($base_url, '/') . '/spend/cards' . $pagination_param;

        // Make the API request
        return $this->make_api_request($url, $api_token);
    }

    public function get_card_sync(Request $request) {
        set_time_limit(600);
        $allcards = [];
        $pagination = '';
    
        do {
            try {
                // Fetch card data from API
                $data = $this->get_card_data($request, $pagination);
    
                // Check if the response contains the expected structure
                if (isset($data['results'])) {
                    foreach ($data['results'] as $cardData) {
                        $existingCard = Card::where('bill_card_id', $cardData['id'])->first();
    
                        // If a duplicate is found, log it and break out of the loop
                        if ($existingCard) {
                            Log::info('Duplicate card found, stopping further data collection for api_id: ' . $cardData['id']);
                            break 2; // Break out of both the foreach loop and the do-while loop
                        }
    
                        // Insert or update the card information in the database
                        Card::updateOrCreate(
                            ['bill_card_id' => $cardData['id']], // Match based on 'bill_card_id'
                            [
                                'name' => $cardData['name'] ?? null, // Use null if the key is missing
                                'user_id' => $cardData['userId'] ?? null,
                                'budget_id' => $cardData['budgetId'] ?? null,
                                'last_four' => $cardData['lastFour'] ?? null,
                                'valid_thru' => $cardData['validThru'] ?? null,
                                'expiration_date' => $cardData['expirationDate'] ?? null,
                                'status' => $cardData['status'] ?? null,
                                'type' => $cardData['type'] ?? null,
                                'share_budget_funds' => $cardData['shareBudgetFunds'] ?? false,
                                'recurring' => $cardData['recurring'] ?? false,
                                'recurring_limit' => $cardData['recurringLimit'] ?? 0,
                                'current_period_limit' => $cardData['currentPeriod']['limit'] ?? 0,
                                'current_period_spent' => $cardData['currentPeriod']['spent'] ?? 0,
                                'created_time' => $cardData['createdTime'] ?? null,
                                'updated_time' => $cardData['updatedTime'] ?? null,
                            ]
                        );
                    }
    
                    // Merge results to keep track of all the cards retrieved
                    $allcards = array_merge($allcards, $data['results']);
                    $pagination = $data['nextPage'] ?? null;
                } else {
                    Log::warning('Unexpected API response structure', ['response' => $data]);
                    break; // Exit if the expected results are not present
                }
            } catch (\Exception $e) {
                Log::error('Error retrieving card data', ['message' => $e->getMessage()]);
                break; // Exit on error
            }
        } while ($pagination);
    
        // Return all cards
        return response()->json($allcards);
    }

    public function get_card_list(){
        $allcards = Card::all();

        // Return all cards
        return response()->json($allcards);
    }
}
