<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RfidReaderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info('RfidReaderMiddleware executed.');

        $rfid = $request->input('rfid');
        Log::info('RFID value: ' . $rfid);

        if (empty($rfid)) {
            Log::info('RFID not detected.');
            return redirect()->back()->with('error', 'RFID not detected. Please scan your RFID card.');
        }

        // You can add further logic to validate the RFID value here

        return $next($request);
    }
}

