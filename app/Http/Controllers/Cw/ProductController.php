<?php

namespace App\Http\Controllers\Cw;

use App\Http\Controllers\Controller;
use App\Models\Bill\AccessPrivileges;
use App\Models\Cw\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function get_access_token() {
        return AccessPrivileges::where('api_environment', 'production')
            ->where('source_platform', 'connect-wise')
            ->where('status', 1)
            ->first();
    }

    private function make_api_request($api_path) {
        try {
            // Get access token and decode required values
            $access_token = $this->get_access_token();
            $company = $access_token->company_name;
            $publicKey = base64_decode($access_token->public_key);
            $privateKey = base64_decode($access_token->private_key);
            $authString = base64_encode("$company+$publicKey:$privateKey");
            $client_id = base64_decode($access_token->client_id);
            
            // Build the API endpoint URL
            $apiHost = "https://na.myconnectwise.net/v4_6_release/apis/3.0";
            $apiEndpoint = rtrim($apiHost, '/') . $api_path;
    
            // Path to the 'cacert.pem' file
            $certPath = Storage::path('cacert.pem');  // Ensure 'cacert.pem' is in the storage path
    
            // Make the API request with the specified certificate and headers
            $response = Http::withHeaders([
                'Authorization' => "Basic $authString",
                'clientid' => $client_id,
                'Accept' => 'application/json',
            ])->withOptions([
                'verify' => $certPath, // Specify the CA bundle
            ])->get($apiEndpoint);
    
            if ($response->failed()) {
                Log::error('API request failed', [
                    'url' => $apiEndpoint,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                throw new \Exception('API request failed with status: ' . $response->status());
            }
    
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error during API request', ['message' => $e->getMessage()]);
            throw new \Exception('Failed to make API request: ' . $e->getMessage());
        }
    }

    public function cw_products_syc(Request $request) {
        try {
            // Build the API path for procurement products
            $api_path = '/procurement/products';
    
            // Make the API request
            $data = $this->make_api_request($api_path);
    
            // Log the success response
            Log::info('API request successful', ['url' => $api_path, 'response' => $data]);
    
            // Ensure the response contains products
            if (is_array($data)) {
                foreach ($data as $productData) {
                    // Save or update the product in the database
                    Product::updateOrCreate(
                        ['cw_product_id' => $productData['id']],
                        [
                            'catalog_item' => json_encode($productData['catalogItem'] ?? null),
                            'description' => $productData['description'] ?? null,
                            'sequence_number' => $productData['sequenceNumber'] ?? null,
                            'quantity' => $productData['quantity'] ?? null,
                            'unit_of_measure' => json_encode($productData['unitOfMeasure'] ?? null),
                            'price' => $productData['price'] ?? null,
                            'cost' => $productData['cost'] ?? null,
                            'ext_price' => $productData['extPrice'] ?? null,
                            'ext_cost' => $productData['extCost'] ?? null,
                            'margin' => $productData['margin'] ?? null,
                            'agreement_amount' => $productData['agreementAmount'] ?? null,
                            'billable_option' => $productData['billableOption'] ?? null,
                            'taxable_flag' => $productData['taxableFlag'] ?? false,
                            'dropship_flag' => $productData['dropshipFlag'] ?? false,
                            'special_order_flag' => $productData['specialOrderFlag'] ?? false,
                            'phase_product_flag' => $productData['phaseProductFlag'] ?? false,
                            'cancelled_flag' => $productData['cancelledFlag'] ?? false,
                            'quantity_cancelled' => $productData['quantityCancelled'] ?? 0.00,
                            'product_supplied_flag' => $productData['productSuppliedFlag'] ?? false,
                            'calculated_price_flag' => $productData['calculatedPriceFlag'] ?? false,
                            'calculated_cost_flag' => $productData['calculatedCostFlag'] ?? false,
                            'need_to_purchase_flag' => $productData['needToPurchaseFlag'] ?? false,
                            'minimum_stock_flag' => $productData['minimumStockFlag'] ?? false,
                            'po_approved_flag' => $productData['poApprovedFlag'] ?? false,
                            'location' => json_encode($productData['location'] ?? null),
                            'business_unit' => json_encode($productData['businessUnit'] ?? null),
                            'vendor_sku' => $productData['vendorSku'] ?? null,
                            'customer_description' => $productData['customerDescription'] ?? null,
                            'sub_contractor_amount_limit' => $productData['subContractorAmountLimit'] ?? 0.00,
                            'opportunity' => json_encode($productData['opportunity'] ?? null),
                            'forecast_detail_id' => $productData['forecastDetailId'] ?? null,
                            'forecast_status' => json_encode($productData['forecastStatus'] ?? null),
                            'product_class' => $productData['productClass'] ?? null,
                            'tax_code' => json_encode($productData['taxCode'] ?? null),    
                            'company' => json_encode($productData['company'] ?? null),
                            'uom' => $productData['uom'] ?? null,
                            'purchase_date' => isset($productData['purchaseDate']) ? \Carbon\Carbon::parse($productData['purchaseDate']) : null,
                            'list_price' => $productData['listPrice'] ?? null,
                            '_info' => json_encode($productData['_info'] ?? null),

                        ]
                    );
                }
            } else {
                Log::warning('No products found in API response', ['response' => $data]);
                return response()->json(['message' => 'No products found'], 204); // No content
            }
    
            return response()->json(['message' => 'Products synced successfully.'], 200);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in cw_products_syc', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch data from API.'], 500);
        }
    }

    public function get_cw_products() {
        $allProducts = Product::all();

        // Return all products
        return response()->json($allProducts);
    }
    
    
}
