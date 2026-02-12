<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Native\Laravel\Facades\Window;

class NativeController extends Controller
{
    public function close(Request $request)
    {
        try {
            // Close the current window
            Window::close();

            return response()->json(['status' => 'success', 'message' => 'Window closed']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function minimize(Request $request)
    {
        try {
            // Minimize the current window
            Window::minimize();

            return response()->json(['status' => 'success', 'message' => 'Window minimized']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function maximize(Request $request)
    {
        try {
            // Maximize the current window
            Window::maximize();

            return response()->json(['status' => 'success', 'message' => 'Window maximized']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
