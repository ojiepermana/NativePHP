<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BniApiService
{
    public function __construct(
        private readonly BniEncryptionService $encryption,
        private readonly string $baseUrl,
        private readonly string $prefix,
        private readonly int $timeout = 30
    ) {}

    /**
     * Send encrypted data to BNI eCollection API
     */
    public function sendRequest(array $data, ?string $requestType = null): array
    {
        try {
            // Encrypt the data
            $encryptedData = $this->encryption->encrypt($data);

            // Prepare request payload
            $payload = [
                'client_id' => config('services.bni.client_id'),
                'prefix' => $this->prefix,
                'data' => $encryptedData,
            ];

            // Add type if specified (for different operations)
            if ($requestType !== null) {
                $payload['type'] = $requestType;
            }

            // Send POST request
            $response = Http::timeout($this->timeout)
                ->acceptJson()
                ->asJson()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl, $payload);

            // Log for debugging (optional)
            Log::channel('daily')->info('BNI API Request', [
                'type' => $requestType ?? 'default',
                'status' => $response->status(),
            ]);

            // Handle response
            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::channel('daily')->error('BNI API Error', [
                'type' => $requestType ?? 'default',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle API response
     */
    private function handleResponse(Response $response): array
    {
        // Check if request was successful
        if (! $response->successful()) {
            throw new \RuntimeException(
                'BNI API request failed: '.$response->body(),
                $response->status()
            );
        }

        $responseData = $response->json();

        // Decrypt response data if present
        if (isset($responseData['data'])) {
            $decrypted = $this->encryption->decrypt($responseData['data']);

            if ($decrypted === null) {
                throw new \RuntimeException('Failed to decrypt BNI API response');
            }

            $responseData['data'] = $decrypted;
        }

        return $responseData;
    }

    /**
     * Validate billing data
     */
    private function validateBillingData(array $data): void
    {
        $requiredFields = [
            'trx_id',
            'trx_amount',
            'billing_type',
            'customer_name',
            'customer_email',
            'customer_phone',
            'virtual_account',
            'datetime_expired',
            'description',
        ];

        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (! isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                $missingFields[] = $field;
            }
        }

        if (! empty($missingFields)) {
            throw new \InvalidArgumentException(
                'Missing required fields for billing: '.implode(', ', $missingFields)
            );
        }

        // Validate billing_type
        if (! in_array($data['billing_type'], ['c', 'o', 'd'])) {
            throw new \InvalidArgumentException(
                'Invalid billing_type. Must be one of: c (close), o (open), d (installment)'
            );
        }

        // Validate email format
        if (! filter_var($data['customer_email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                'Invalid customer_email format'
            );
        }

        // Validate trx_amount is numeric
        if (! is_numeric($data['trx_amount'])) {
            throw new \InvalidArgumentException(
                'Invalid trx_amount. Must be numeric'
            );
        }

        // Validate datetime_expired format (YYYYMMDDHHmmss)
        if (! preg_match('/^\d{14}$/', $data['datetime_expired'])) {
            throw new \InvalidArgumentException(
                'Invalid datetime_expired format. Must be YYYYMMDDHHmmss (e.g., 20240228235959)'
            );
        }
    }

    /**
     * Create bill/inquiry (createbilling)
     */
    public function createBilling(array $billingData): array
    {
        // Validate billing data
        $this->validateBillingData($billingData);

        // Add type to data
        $billingData['type'] = 'createbilling';

        return $this->sendRequest($billingData);
    }

    /**
     * Update bill (updatebilling)
     */
    public function updateBilling(array $billingData): array
    {
        // Add type to data
        $billingData['type'] = 'updatebilling';

        return $this->sendRequest($billingData);
    }

    /**
     * Inquiry bill (inquirybilling)
     */
    public function inquiryBilling(array $inquiryData): array
    {
        // Validate required field
        if (! isset($inquiryData['trx_id']) || $inquiryData['trx_id'] === '') {
            throw new \InvalidArgumentException('trx_id is required for inquiry');
        }

        // Add type to data
        $inquiryData['type'] = 'inquirybilling';

        return $this->sendRequest($inquiryData);
    }

    /**
     * Inquiry payment (inquirypayment)
     */
    public function inquiryPayment(array $inquiryData): array
    {
        // Validate required field
        if (! isset($inquiryData['trx_id']) || $inquiryData['trx_id'] === '') {
            throw new \InvalidArgumentException('trx_id is required for payment inquiry');
        }

        // Add type to data
        $inquiryData['type'] = 'inquirypayment';

        return $this->sendRequest($inquiryData);
    }

    /**
     * Send raw request (for custom operations)
     */
    public function sendRawRequest(array $data): array
    {
        return $this->sendRequest($data);
    }
}
