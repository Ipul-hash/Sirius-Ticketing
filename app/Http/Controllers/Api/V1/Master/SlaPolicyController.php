<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Enums\TicketPriority;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SlaPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlaPolicyController extends Controller
{
    public function index(Request $request, ?string $tenant = null): JsonResponse
    {
        $companyId = $this->resolveCompanyId($request, $tenant);
        if ($companyId === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant perusahaan tidak ditemukan atau tidak valid.',
            ], 404);
        }

        $policies = SlaPolicy::where('company_id', $companyId)->get();

        // Jika tenant belum memiliki kebijakan SLA, lakukan auto-seeding default standard
        if ($policies->isEmpty()) {
            $defaultSlas = [
                ['priority' => TicketPriority::Low, 'first_response' => 1440, 'resolution' => 2880],
                ['priority' => TicketPriority::Medium, 'first_response' => 480, 'resolution' => 1440],
                ['priority' => TicketPriority::High, 'first_response' => 120, 'resolution' => 480],
                ['priority' => TicketPriority::Urgent, 'first_response' => 30, 'resolution' => 120],
            ];

            foreach ($defaultSlas as $sla) {
                SlaPolicy::create([
                    'company_id' => $companyId,
                    'priority' => $sla['priority'],
                    'first_response_time_minutes' => $sla['first_response'],
                    'resolution_time_minutes' => $sla['resolution'],
                ]);
            }

            $policies = SlaPolicy::where('company_id', $companyId)->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar kebijakan SLA berhasil diambil.',
            'data' => $policies,
        ], 200);
    }

    public function update(Request $request, string $tenant, string $priority): JsonResponse
    {
        $companyId = $this->resolveCompanyId($request, $tenant);
        if ($companyId === null) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant perusahaan tidak ditemukan atau tidak valid.',
            ], 404);
        }

        $priorityEnum = TicketPriority::tryFrom(strtolower($priority));
        if ($priorityEnum === null) {
            return response()->json([
                'success' => false,
                'message' => 'Prioritas SLA tidak valid. Prioritas yang diizinkan: low, medium, high, urgent.',
            ], 422);
        }

        $validated = $request->validate([
            'first_response_time_minutes' => 'required|integer|min:1',
            'resolution_time_minutes' => 'required|integer|gte:first_response_time_minutes',
        ], [
            'resolution_time_minutes.gte' => 'Target waktu penyelesaian (resolusi) harus lebih besar atau sama dengan target waktu respon awal.',
        ]);

        $slaPolicy = SlaPolicy::updateOrCreate(
            [
                'company_id' => $companyId,
                'priority' => $priorityEnum,
            ],
            [
                'first_response_time_minutes' => $validated['first_response_time_minutes'],
                'resolution_time_minutes' => $validated['resolution_time_minutes'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Kebijakan SLA untuk prioritas {$priorityEnum->value} berhasil diperbarui.",
            'data' => $slaPolicy,
        ], 200);
    }

    protected function resolveCompanyId(Request $request, ?string $tenant = null): ?int
    {
        if ($tenant !== null && $tenant !== '') {
            $company = Company::where('slug', $tenant)
                ->orWhere('id', $tenant)
                ->first();

            if ($company !== null) {
                return (int) $company->id;
            }
        }

        if ($request->filled('company_id')) {
            return (int) $request->input('company_id');
        }

        if ($request->user() !== null && $request->user()->company_id !== null) {
            return (int) $request->user()->company_id;
        }

        return null;
    }
}
