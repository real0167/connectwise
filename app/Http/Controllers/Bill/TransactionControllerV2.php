<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Models\Bill\AccessPrivileges;
use App\Models\Bill\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TransactionControllerV2 extends Controller
{
    public function get_access_token()
    {
        return AccessPrivileges::where('api_environment', 'production')
            ->where('status', 1)
            ->first(['api_token', 'env_base_url']);
    }

    private function make_api_request($url, $api_token)
    {
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

    public function get_transaction_data($request, $pagination)
    {
        $access_token = $this->get_access_token();

        // Decode tokens
        $api_token = base64_decode($access_token->api_token);
        $base_url = base64_decode($access_token->env_base_url);

        // Build the API URL
        $pagination_param = $pagination ? "?nextPage=$pagination" : '';
        $url = rtrim($base_url, '/') . '/spend/transactions' . $pagination_param;

        // Make the API request
        return $this->make_api_request($url, $api_token);
    }

    public function get_transaction_sync(Request $request)
    {
        set_time_limit(6000);
        $allTransactions = [];
        $pagination = '';

        do {
            try {
                // Fetch transaction data from API
                $data = $this->get_transaction_data($request, $pagination);

                // Check if the response contains the expected structure
                if (isset($data['results'])) {
                    foreach ($data['results'] as $transactionData) {
                        $existingTransaction = Transaction::where('bill_transaction_id', $transactionData['id'])->first();

                        // If a duplicate is found, log it and break out of the loop
                        if ($existingTransaction) {
                            Log::info('Duplicate transaction found, stopping further data collection for bill_transaction_id: ' . $transactionData['id']);
                            break 2; // Break out of both the foreach loop and the do-while loop
                        }

                        // Insert or update the transaction information in the database
                        Transaction::updateOrCreate(
                            ['bill_transaction_id' => $transactionData['id']], // Match based on 'bill_transaction_id'
                            [
                                'user_id' => $transactionData['userId'] ?? null,
                                'transaction_type' => $transactionData['transactionType'] ?? null,
                                'budget_id' => $transactionData['budgetId'] ?? null,
                                'raw_merchant_name' => $transactionData['rawMerchantName'] ?? null,
                                'merchant_name' => $transactionData['merchantName'] ?? null,
                                'is_locked' => $transactionData['isLocked'] ?? false,
                                'is_reconciled' => $transactionData['isReconciled'] ?? false,
                                'receipt_required' => $transactionData['receiptRequired'] ?? false,
                                'review_required' => $transactionData['reviewRequired'] ?? false,
                                'complete' => $transactionData['complete'] ?? false,
                                'network' => $transactionData['network'] ?? null,
                                'is_parent' => $transactionData['isParent'] ?? false,
                                'amount' => $transactionData['amount'] ?? 0,
                                'transacted_amount' => $transactionData['transactedAmount'] ?? 0,
                                'fees' => $transactionData['fees'] ?? 0,
                                'receipt_status' => $transactionData['receiptStatus'] ?? null,
                                'receipt_sync_status' => $transactionData['receiptSyncStatus'] ?? null,
                                'merchant_category_code' => $transactionData['merchantCategoryCode'] ?? null,
                                'card_present' => $transactionData['cardPresent'] ?? false,
                                'card_id' => $transactionData['cardId'] ?? null,

                                // JSON fields
                                'currency_data' => json_encode($transactionData['currencyData'] ?? []),
                                'tags' => json_encode($transactionData['tags'] ?? []),
                                'custom_fields' => json_encode($transactionData['customFields'] ?? []),
                                'child_transaction_ids' => json_encode($transactionData['childTransactionIds'] ?? []),
                                'reviews' => json_encode($transactionData['reviews'] ?? []),
                                'reviewers' => json_encode($transactionData['reviewers'] ?? []),
                                'occurred_time' => $transactionData['occurredTime'] ?? null,
                                'updated_time' => $transactionData['updatedTime'] ?? null,
                            ]
                        );
                    }

                    // Merge results to keep track of all the transactions retrieved
                    $allTransactions = array_merge($allTransactions, $data['results']);
                    $pagination = $data['nextPage'] ?? null;
                } else {
                    Log::warning('Unexpected API response structure', ['response' => $data]);
                    break; // Exit if the expected results are not present
                }
            } catch (\Exception $e) {
                Log::error('Error retrieving transaction data', ['message' => $e->getMessage()]);
                break; // Exit on error
            }
        } while ($pagination);

        // Return all transactions
        return response()->json($allTransactions);
    }

    public function get_transaction_list()
    {
        // $allTransactions = Transaction::all();

        // // Return all cards
        // return response()->json($allTransactions);

        $transactions = Transaction::with(['card' => function ($query) {
            $query->with(['products']); // Load related products for each card
        }])->get();
            

        return response()->json($transactions);
    }
}
