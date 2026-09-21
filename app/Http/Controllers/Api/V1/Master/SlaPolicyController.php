<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Http\Controllers\Controller;
use App\Models\SlaPolicy;
use App\Enums\TicketPriority;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SlaPolicyController extends Controller
{
    public function index(Request $request, $tenant)
    {
        $policies = SlaPolicy::where('company_id', $tenant)->get();

        return response()->json([
            'message' => 'Daftar kebijakan SLA berhasil dimuat',
            'data'    => $policies
        ]);
    }

    public function update(Request $request, $tenant, $priority)
    {
        $validated = $request->validate([
            'first_response_time_minutes' => 'required|integer|min:1',
            'resolution_time_minutes'     => 'required|integer|min:1',
        ]);

        $slaPolicy = SlaPolicy::updateOrCreate(
            [
                'company_id' => $tenant,
                'priority'   => $priority,
            ],
            [
                'first_response_time_minutes' => $validated['first_response_time_minutes'],
                'resolution_time_minutes'     => $validated['resolution_time_minutes'],
            ]
        );

        return response()->json([
            'message' => 'Kebijakan SLA berhasil diperbarui',
            'data'    => $slaPolicy
        ]);
    }
}