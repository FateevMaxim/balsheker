<?php

namespace App\Http\Controllers;

use App\Services\Cargo786ApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

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

        $statusCode = $result['success'] ? 200 : ($result['code'] ?? 500);

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
            'uid' => 'nullable|string',
            'start_local' => 'required|string',
            'target_local' => 'required|string',
            'contact_name' => 'nullable|string|max:255',
            'contact_tel' => 'nullable|string|max:50',
            'contact_addr' => 'nullable|string|max:500',
            'pickup_type' => 'nullable|string|in:1,2',
            'pickup_name' => 'nullable|string|max:255',
            'pickup_tel' => 'nullable|string|max:50',
            'pickup_addr' => 'nullable|string|max:500',
            'goods' => 'required|array',
            'goods.*.goods_name' => 'required|string|max:255',
            'goods.*.goods_price' => 'required|numeric|min:0',
            'goods.*.goods_num' => 'required|integer|min:1',
            'packages' => 'required|array',
            'packages.*.express_sn' => 'required|string|max:255',
            'remark' => 'nullable|string|max:1000',
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

        $result = $this->cargo786Service->createOrder($validated, $lang);

        $statusCode = $result['success'] ? 200 : ($result['code'] ?? 500);

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

        $statusCode = $result['success'] ? 200 : ($result['code'] ?? 500);

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

        return response()->json([
            'success' => $result['success'],
            'code' => $result['code'],
            'msg' => $result['success'] ? 'Connection successful' : 'Connection failed: ' . $result['msg'],
            'test_result' => $result
        ]);
    }
}
