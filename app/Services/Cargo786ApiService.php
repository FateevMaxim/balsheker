<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Cargo786ApiService
{
    private string $baseUrl;
    private string $accountKey;
    private string $clientSecret;

    public function __construct(bool $useProduction = false)
    {
        $this->baseUrl = $useProduction ? self::PROD_BASE_URL : self::TEST_BASE_URL;
        $this->accountKey = self::BAL_ACCOUNT_KEY;
        $this->clientSecret = self::BAL_CLIENT_SECRET;
    }

    /**
     * Generate MD5 signature for API authentication
     *
     * @param array $parameters All request parameters
     * @return string MD5 signature in uppercase
     */
    private function generateSignature(array $parameters): string
    {
        // Sort parameters by key name in ASCII order
        ksort($parameters, SORT_NATURAL | SORT_FLAG_CASE);

        $signParts = [];
        $signParts[] = $this->clientSecret;

        foreach ($parameters as $key => $value) {
            $signParts[] = "{$key}{$value}";
        }

        $signParts[] = $this->clientSecret;
        $signString = implode('', $signParts);

        return strtoupper(md5($signString));
    }

    /**
     * Get public parameters required for all API requests
     *
     * @param string $lang Language (zh/kk/ru)
     * @return array Public parameters
     */
    private function getPublicParameters(string $lang = 'ru'): array
    {
        return [
            'account_key' => $this->accountKey,
            'operating_time' => time(),
            'lang' => $lang
        ];
    }

    /**
     * Make authenticated API request
     *
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @param string $method HTTP method
     * @param string $lang Language
     * @return array API response
     */
    private function makeRequest(string $endpoint, array $data = [], string $method = 'POST', string $lang = 'ru'): array
    {
        $publicParams = $this->getPublicParameters($lang);
        $allParams = array_merge($publicParams, $data);

        // Generate signature
        $signature = $this->generateSignature($allParams);
        $allParams['sign'] = $signature;

        $url = $this->baseUrl . $endpoint;

        try {
            $response = Http::asForm()->$method($url, $allParams);

            if ($response->successful()) {
                return $response->json();
            }

            // Log error (compatible with standalone usage)
            if (class_exists('Illuminate\Support\Facades\Log') && function_exists('app') && app()->bound('log')) {
                Log::error('Cargo786 API Error', [
                    'url' => $url,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            } else {
                error_log("Cargo786 API Error: {$url} - Status: {$response->status()} - Response: {$response->body()}");
            }

            return [
                'success' => false,
                'code' => $response->status(),
                'msg' => 'API request failed',
                'data' => []
            ];

        } catch (\Exception $e) {
            // Log error (compatible with standalone usage)
            if (class_exists('Illuminate\Support\Facades\Log') && function_exists('app') && app()->bound('log')) {
                Log::error('Cargo786 API Exception', [
                    'url' => $url,
                    'error' => $e->getMessage()
                ]);
            } else {
                error_log("Cargo786 API Exception: {$url} - Error: {$e->getMessage()}");
            }

            return [
                'success' => false,
                'code' => 500,
                'msg' => 'Request exception: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Create a new member (user registration)
     *
     * @param string $name Full name of the user
     * @param string $account Login account (phone/email)
     * @param string $password Password
     * @param string|null $areaCode Phone area code (required for phone registration)
     * @param string $lang Language
     * @return array API response
     */
    public function createMember(string $name, string $account, string $password, ?string $areaCode = null, string $lang = 'ru'): array
    {
        $data = [
            'name' => $name,
            'account' => $account,
            'password' => $password
        ];

        if ($areaCode) {
            $data['area_code'] = $areaCode;
        }

        return $this->makeRequest('/appsapi/createMember', $data, 'POST', $lang);
    }

    /**
     * Create an order
     *
     * @param array $orderData Order data
     * @param string $lang Language
     * @return array API response
     */
    public function createOrder(array $orderData, string $lang = 'ru'): array
    {
        return $this->makeRequest('/appsapi/createOrder', $orderData, 'POST', $lang);
    }

    /**
     * Get address list
     *
     * @param string $lang Language
     * @return array API response
     */
    public function getAddressList(string $lang = 'ru'): array
    {
        return $this->makeRequest('/appsapi/addressList', [], 'GET', $lang);
    }
}
