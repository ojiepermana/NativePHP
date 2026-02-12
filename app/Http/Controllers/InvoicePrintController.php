<?php

namespace App\Http\Controllers;

use App\Actions\GenerateInvoiceHtmlAction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoicePrintController extends Controller
{
    public function __construct(
        private GenerateInvoiceHtmlAction $generateInvoiceHtmlAction
    ) {}

    /**
     * Display invoice in HTML format
     */
    public function show(Request $request, string $id_faktur): Response|\Illuminate\Http\JsonResponse
    {
        // Validate output parameter
        $output = $request->query('output', 'html');

        if ($output !== 'html') {
            return response()->json([
                'error' => 'Only HTML output is supported in this application',
                'message' => 'Please remove the output parameter or set it to html',
            ], 400);
        }

        // Check if save action is requested
        if ($request->query('save') === 'file') {
            $path = $this->generateInvoiceHtmlAction->saveToFile($id_faktur);

            return response()->json([
                'success' => true,
                'message' => 'Invoice HTML saved to file',
                'path' => $path,
            ]);
        }

        // Generate and display HTML
        $html = $this->generateInvoiceHtmlAction->execute($id_faktur);

        return response($html)->header('Content-Type', 'text/html');
    }
}
