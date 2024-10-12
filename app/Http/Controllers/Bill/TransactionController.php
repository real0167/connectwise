<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Bill\CardController;
use App\Http\Controllers\Controller;
use App\Models\Bill\AccessPrivileges;
use App\Models\Cw\TransactionModel;
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

        $this->insert_to_db($response);
        // Close cURL session
        curl_close($ch);
        return $response;
    }

    /*
     * this function called in get_transaction_list function
     */
    public function insert_to_db($response) {
        $data = json_decode($response); $count = 0;
        foreach ($data->results as $dt) {
            $crdCtrl = new CardController();
            $identifier_data = $crdCtrl->get_card_details($dt->cardId);
            $identifier = json_decode($identifier_data);
            TransactionModel::Create([
                "tran_id" => $dt->id,
                "is_locked" => $dt->isLocked,
                "is_reconciled" => $dt->isReconciled,
                "transaction_type" => $dt->transactionType,
                "user_id" => base64_decode($dt->userId),
                "raw_merchant_name" => $dt->rawMerchantName,
                "merchant_name" => $dt->merchantName,
                "budget_id" => base64_decode($dt->budgetId),
                "original_currency_amount" => $dt->currencyData->originalCurrencyAmount,
                "original_currency_code" => $dt->currencyData->originalCurrencyCode,
                "receipt_required" => $dt->receiptRequired,
                "review_required" => $dt->reviewRequired,
                "occured_time" => Carbon::parse($dt->occurredTime)->toDateTimeString(),
                "updated_time" => Carbon::parse($dt->updatedTime)->toDateTimeString(),
                "complete" => $dt->complete,
                "network" => $dt->network,
                "is_parent" => $dt->isParent,
                "amount" => $dt->amount,
                "transacted_amount" => $dt->transactedAmount,
                "fees" => $dt->fees,
                "receipt_status" => $dt->receiptStatus,
                "card_id" => $dt->cardId,
                "identifier_from_bill" => $identifier->name, //base64_decode($dt->cardId),
                "receipt_sync_status" => $dt->receiptSyncStatus,
                "merchant_category_code" => $dt->merchantCategoryCode,
                "created_at" => Carbon::parse(now())->toDateTimeString(),
                "updated_at" => Carbon::parse(now())->toDateTimeString()
            ]);
            $count++;
        }
        /*if($count > 0) {
            return response()->json([
                'response_code' => 201,
                'message' => "$count data successfully inserted"
            ]);
        }*/
    }
}
