# BNI eCollection API Integration

Service untuk integrasi dengan BNI eCollection API menggunakan enkripsi custom BNI.

## Configuration

Tambahkan konfigurasi berikut ke file `.env`:

```env
BNI_CLIENT_ID=your_bni_client_id
BNI_SECRET=your_bni_secret_key
BNI_PREFIX=8
BNI_BASE_URL=https://apibeta.bni-ecollection.com
BNI_TIMEOUT=30
```

**Configuration Details:**
- `BNI_CLIENT_ID` - Client ID yang diberikan BNI
- `BNI_SECRET` - Secret key untuk enkripsi
- `BNI_PREFIX` - Prefix untuk virtual account (biasanya "8" atau sesuai ketentuan BNI)
- `BNI_BASE_URL` - Base URL endpoint BNI API
- `BNI_TIMEOUT` - Request timeout dalam detik

**Environment:**
- Development/Testing: `https://apibeta.bni-ecollection.com`
- Production: `https://api.bni-ecollection.com`

**Note:** BNI eCollection API menggunakan single endpoint. Semua request dikirim ke endpoint yang sama dengan method POST. Perbedaan operasi ditentukan dari parameter `type` dalam payload atau dari struktur data yang dikirim.

**Request Payload Structure:**
```json
{
  "client_id": "your_client_id",
  "prefix": "8",
  "data": "encrypted_data_here",
  "type": "createbilling"
}
```

## Usage

### 1. Direct Service Injection

```php
use App\Services\BniApiService;

class PaymentController extends Controller
{
    public function __construct(
        private readonly BniApiService $bniApi
    ) {}

    public function createVirtualAccount(Request $request)
    {
        $response = $this->bniApi->createBilling([
            'type' => 'createbilling',
            'trx_id' => $request->invoice_number,
            'trx_amount' => (string) $request->amount,
            'billing_type' => 'c',
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'virtual_account' => $request->va_number,
            'datetime_expired' => $request->expired_at->format('YmdHis'),
            'description' => $request->description,
        ]);

        return response()->json($response);
    }
}
```

### 2. Using Facade

```php
use App\Facades\BniApi;

// Create Billing (Virtual Account)
$billing = BniApi::createBilling([
    'type' => 'createbilling',
    'trx_id' => '12345',
    'trx_amount' => '100000',
    'billing_type' => 'c',
    'customer_name' => 'John Doe',
    'customer_email' => 'john@example.com',
    'customer_phone' => '08123456789',
    'virtual_account' => '8808000000001234',
    'datetime_expired' => '20240228235959',
    'description' => 'Payment for invoice #12345',
]);

// Update Billing
$update = BniApi::updateBilling([
    'type' => 'updatebilling',
    'trx_id' => '12345',
    'trx_amount' => '150000',
    'customer_name' => 'John Doe Updated',
    'datetime_expired' => '20240228235959',
]);

// Inquiry Billing
$inquiry = BniApi::inquiryBilling([
    'type' => 'inquirybilling',
    'trx_id' => '12345',
]);

// Inquiry Payment
$payment = BniApi::inquiryPayment([
    'type' => 'inquirypayment',
    'trx_id' => '12345',
]);

// Raw Request (if you need to send custom data structure)
$custom = BniApi::sendRawRequest([
    'type'Billing(array $billingData): array
Membuat billing/virtual account baru.

**Parameters:**
- `trx_id` - Transaction ID (unique)
- `trx_amount` - Transaction amount
- `billing_type` - Billing type (c/o/d)
- `customer_name` - Customer name
- `customer_email` - Customer email
- `customer_phone` - Customer phone
- `virtual_account` - Virtual account number
- `datetime_expired` - Expiry date (YYYYMMDDHHmmss)
- `description` - Transaction description

### updateBilling(array $billingData): array
Update billing yang sudah ada.

### inquiryBilling(array $inquiryData): array
Inquiry informasi billing/virtual account.

### inquiryPayment(array $inquiryData): array
Inquiry informasi pembayaran.

### sendRawRequest(array $data): array
Mengirim raw request ke BNI API (untuk operasi custom).

### sendRequest(array $data, string $requestType = null): array
Method internal untuk mengirim request (gunakan methods di atas lebih recommended)$status): array
Update status transaksi.

### getReport(string $startDate, string $endDate): array
Mendapatkan report transaksi.

### sendRequest(string $endpoint, array $data): array
Mengirim custom request ke endpoint BNI.

## Encryption Service

Jika perlu menggunakan enkripsi/dekripsi secara manual:

```php
use App\Facades\BniEncryption;

// Encrypt data
$encrypted = BniEncryption::encrypt([
    'trx_id' => 'TRX001',
    'amount' => 100000,
]);

// Decrypt data
$decrypted = BniEncryption::decrypt($encryptedString);
```

## Error Handling

Service akan throw exception jika terjadi error:

```php
use App\Facades\BniApi;

try {
    $response = BniApi::createInquiry($data);
    
    // Process response
    return response()->json([
        'success' => true,
        'data' => $response,
    ]);
} catch (\RuntimeException $e) {
    Log::error('BNI API Error: '.$e->getMessage());
    
    return response()->json([
        'success' => false,
        'message' => 'Payment gateway error',
    ], 500);
}
```

## Response Format

Response dari BNI API akan otomatis didekripsi:

```php
[
    'stadengan inquiry billing
$response = BniApi::inquiryBilling([
    'type' => 'inquirybilling',
    'trx_id' => 'TEST123'
        // Decrypted data
        'trx_id' => 'TRX001',
        'virtual_account' => '8808123456789012',
        // ...
    ]
]
```

## Logging

Semua request dan error akan otomatis dicatat di log channel `daily`. Periksa di `storage/logs/laravel.log`.

## Testing

```php
use App\Facades\BniApi;

// Test connectivity
$response = BniApi::sendRequest('/api/health-check', [
    'timestamp' => now()->timestamp,
]);
```

## Security Notes

1. Jangan commit file `.env` ke version control
2. Client ID dan Secret harus dijaga kerahasiaannya
3. Gunakan HTTPS untuk semua request
4. Validasi response data sebelum digunakan
5. Set timeout yang sesuai dengan kebutuhan

## Troubleshooting

### Request Timeout
Tingkatkan nilai `BNI_TIMEOUT` di `.env`:
```env
BNI_TIMEOUT=60
```

### Decryption Failed
- Pastikan `BNI_CLIENT_ID` dan `BNI_SECRET` benar
- Periksa timestamp pada response (tidak boleh lebih dari 480 detik)

### Connection Error
- Periksa `BNI_BASE_URL` apakah sudah benar
- Pastikan server dapat mengakses URL BNI
- Periksa firewall/network settings
