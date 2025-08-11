<?php

namespace App\Http\Controllers;

use App\Models\TrackList;
use App\Services\Cargo786ApiService;
use App\Models\DeliverySignoff;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Cargo786Controller extends Controller
{
    private Cargo786ApiService $cargo786Service;

    public function __construct(Cargo786ApiService $cargo786Service)
    {
        $this->cargo786Service = $cargo786Service;
    }

    /**
     * Create a new member in 786 system
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createMember(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'account' => 'required|string|max:255',
            'password' => 'required|string|min:3',
            'area_code' => 'nullable|string|max:10',
            'lang' => 'nullable|string|in:zh,kk,ru'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => 400,
                'msg' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => []
            ], 400);
        }

        $validated = $validator->validated();

        $result = $this->cargo786Service->createMember(
            $validated['name'],
            $validated['account'],
            $validated['password'],
            $validated['area_code'] ?? null,
            $validated['lang'] ?? 'ru'
        );

        // Ensure status code is a valid HTTP status code (100-599)
        $statusCode = $result['success'] ? 200 : ($result['code'] ?? 500);
        if (!is_numeric($statusCode) || $statusCode < 100 || $statusCode >= 600) {
            $statusCode = 500;
        }

        return response()->json($result, $statusCode);
    }

    /**
     * Create an order in 786 system
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_local' => 'required|string',
            'target_local' => 'required|string',
            'packages' => 'required|array',
            'packages.*.express_sn' => 'required|string|max:255',
            'goods' => 'nullable|array',
            'goods.goods_name' => 'required_with:goods|string|max:255',
            'goods.goods_price' => 'required_with:goods|numeric|min:0',
            'goods.goods_num' => 'required_with:goods|integer|min:1',
            'lang' => 'nullable|string|in:zh,kk,ru'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => 400,
                'msg' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => []
            ], 400);
        }

        $validated = $validator->validated();
        $lang = $validated['lang'] ?? 'ru';
        unset($validated['lang']);
        $validated['uuid'] = Str::uuid()->toString();

        $result = $this->cargo786Service->createOrder($validated, $lang);

        // Ensure status code is a valid HTTP status code (100-599)
        $statusCode = $result['success'] ? 200 : ($result['code'] ?? 500);
        if (!is_numeric($statusCode) || $statusCode < 100 || $statusCode >= 600) {
            $statusCode = 500;
        }
        Log::info('Cargo786 API Response: ' . json_encode($result['data'], JSON_UNESCAPED_UNICODE));
        foreach ($result['data'] as $item) {
            $packageSn = $item['package_sn'];
            $expressSn = $item['express_sn'];
            DeliverySignoff::create([
                'express_sn' => $expressSn,
                'package_sn' => $packageSn
            ]);
        }

        return response()->json($result, $statusCode);
    }

    /**
     * Get address list from 786 system
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAddressList(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'lang' => 'nullable|string|in:zh,kk,ru'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => 400,
                'msg' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => []
            ], 400);
        }

        $lang = $request->input('lang', 'ru');

        $result = $this->cargo786Service->getAddressList($lang);

        // Ensure status code is a valid HTTP status code (100-599)
        $statusCode = $result['success'] ? 200 : ($result['code'] ?? 500);
        if (!is_numeric($statusCode) || $statusCode < 100 || $statusCode >= 600) {
            $statusCode = 500;
        }

        return response()->json($result, $statusCode);
    }

    /**
     * Test connection to 786 API
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function testConnection(Request $request): JsonResponse
    {
        $lang = $request->input('lang', 'ru');

        // Test with address list as it's a simple GET request
        $result = $this->cargo786Service->getAddressList($lang);

        // Ensure we use a valid HTTP status code
        $statusCode = 200; // Default status code for the test connection response

        return response()->json([
            'success' => $result['success'],
            'code' => $result['code'],
            'msg' => $result['success'] ? 'Connection successful' : 'Connection failed: ' . $result['msg'],
            'test_result' => $result
        ], $statusCode);
    }

    /**
     * Handle delivery sign-off callback from 786 system
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deliverySignoffCallback(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
/*            // Public parameters
            'account_key' => 'required|string',
            'sign' => 'required|string',
            'operating_time' => 'required|numeric',
            'lang' => 'required|string|in:zh,kk,ru',*/

            // Business parameters
            'express_sn' => 'required|string|max:255',
            'height' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'volume' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
            'freight_price' => 'numeric|min:0',
            'freight_cost' => 'numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => 400,
                'msg' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => []
            ], 400);
        }

        $validated = $validator->validated();

        // Verify signature
/*        $signatureValid = $this->verifyCallbackSignature($validated);

        if (!$signatureValid) {
            return response()->json([
                'success' => false,
                'code' => 10022,
                'msg' => 'Signature error',
                'data' => []
            ], 400);
        }*/

        // Save to database
        try {
            DeliverySignoff::query()->where('express_sn', $validated['express_sn'])->update([
                'express_sn' => $validated['express_sn'],
                'height' => $validated['height'] ?? null,
                'width' => $validated['width'] ?? null,
                'length' => $validated['length'] ?? null,
                'volume' => $validated['volume'],
                'weight' => $validated['weight'],
                'freight_price' => $validated['freight_price'],
                'freight_cost' => $validated['freight_cost']
            ]);

            TrackList::query()->create([
                'track_code' => $validated['express_sn'],
                'to_china' => date(now()),
                'status' => 'Получено в Китае',
                'reg_china' => 1,
                'created_at' => date(now()),
            ]);

            return response()->json([
                'success' => true,
                'code' => 200,
                'msg' => 'Successfully signed for'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'msg' => 'Database error: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Verify callback signature using MD5 algorithm
     *
     * @param array $parameters
     * @return bool
     */
    private function verifyCallbackSignature(array $parameters): bool
    {
        $receivedSignature = $parameters['sign'];
        unset($parameters['sign']); // Remove sign from parameters for verification

        // Sort parameters by key name in ASCII order
        ksort($parameters, SORT_NATURAL | SORT_FLAG_CASE);

        // Get client secret based on account_key
        $clientSecret = $this->getClientSecretByAccountKey($parameters['account_key']);

        if (!$clientSecret) {
            return false;
        }

        // Build signature string
        $signParts = [];
        $signParts[] = $clientSecret;

        foreach ($parameters as $key => $value) {
            $signParts[] = "{$key}{$value}";
        }

        $signParts[] = $clientSecret;
        $signString = implode("", $signParts);
        $calculatedSignature = strtoupper(md5($signString));

        return $calculatedSignature === $receivedSignature;
    }

    /**
     * Get client secret by account key
     *
     * @param string $accountKey
     * @return string|null
     */
    private function getClientSecretByAccountKey(string $accountKey): ?string
    {
        // Based on documentation credentials
        $credentials = [
            'HTad4l5jyfjdotgdsp' => 'FFA9BC040DE9F05F27370FB4275D9F8D'
        ];

        return $credentials[$accountKey] ?? null;
    }
}
