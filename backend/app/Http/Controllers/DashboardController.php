<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'customer_satisfaction_rate' => '96%',
                'total_conversations' => 4892,
                'answered_inquiries' => 4320,
                'avg_response_time' => '1m 23s',
                'new_inquiries' => 572,
                'resolution_rate' => 93
            ]
        ]);
    }
}
