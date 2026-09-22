<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Http\Controllers\Controller;
use App\Models\CannedResponse;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CannedResponseController extends Controller
{
    /**
     * Menampilkan daftar template balasan cepat dengan filter dan pagination.
     */
    public function index(Request $request, ?string $tenant = null): JsonResponse
    {
        $companyId = $this->resolveCompanyId($request, $tenant);

        $query = CannedResponse::query()
            ->with([
                'company:id,name,slug',
                'department:id,name',
            ]);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('shortcut', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $deptId = $request->query('department_id');
            if ($deptId === 'global' || $deptId === 'null') {
                $query->whereNull('department_id');
            } else {
                $query->where('department_id', $deptId);
            }
        }

        $sortBy = $request->query('sort_by', 'title');
        $sortOrder = $request->query('sort_order', 'asc');
        if (in_array($sortBy, ['id', 'title', 'shortcut', 'created_at'], true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        if ($request->query('paginate') === 'false') {
            $responses = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar semua template balasan cepat berhasil dimuat.',
                'data' => $responses,
            ], 200);
        }

        $perPage = (int) $request->query('per_page', 10);
        $responses = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar template balasan cepat berhasil dimuat.',
            'data' => $responses->items(),
            'meta' => [
                'current_page' => $responses->currentPage(),
                'last_page' => $responses->lastPage(),
                'per_page' => $responses->perPage(),
                'total' => $responses->total(),
            ],
        ], 200);
    }

    /**
     * Menyimpan template balasan cepat baru ke database.
     */
    public function store(Request $request, ?string $tenant = null): JsonResponse
    {
        $companyId = $this->resolveCompanyId($request, $tenant);

        // Format shortcut agar selalu berawalan '/'
        if ($request->filled('shortcut')) {
            $shortcut = trim($request->input('shortcut'));
            if (! str_starts_with($shortcut, '/')) {
                $shortcut = '/'.$shortcut;
            }
            $request->merge(['shortcut' => $shortcut]);
        }

        $validated = $request->validate([
            'company_id' => [
                $companyId !== null ? 'nullable' : 'required',
                'integer',
                'exists:companies,id',
            ],
            'title' => 'required|string|max:255',
            'shortcut' => [
                'required',
                'string',
                'max:100',
                Rule::unique('canned_responses', 'shortcut')->where(function ($query) use ($companyId, $request) {
                    $targetCompanyId = $companyId ?? $request->input('company_id');
                    if ($targetCompanyId !== null) {
                        $query->where('company_id', $targetCompanyId);
                    }
                }),
            ],
            'message' => 'required|string',
            'department_id' => [
                'nullable',
                'integer',
                Rule::exists('departments', 'id')->where(function ($query) use ($companyId, $request) {
                    $targetCompanyId = $companyId ?? $request->input('company_id');
                    if ($targetCompanyId !== null) {
                        $query->where('company_id', $targetCompanyId);
                    }
                }),
            ],
        ]);

        $finalCompanyId = $companyId ?? (int) $validated['company_id'];

        $cannedResponse = CannedResponse::create([
            'company_id' => $finalCompanyId,
            'department_id' => ! empty($validated['department_id']) ? (int) $validated['department_id'] : null,
            'title' => trim($validated['title']),
            'shortcut' => trim($validated['shortcut']),
            'message' => $validated['message'],
        ]);

        $cannedResponse->load(['company:id,name,slug', 'department:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Template balasan cepat berhasil dibuat.',
            'data' => $cannedResponse,
        ], 201);
    }

    /**
     * Menampilkan detail satu template balasan cepat.
     */
    public function show(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $targetId = $param2 !== null ? $param2 : $param1;

        $companyId = $this->resolveCompanyId($request, $tenant);

        $cannedResponse = CannedResponse::query()
            ->with(['company:id,name,slug', 'department:id,name'])
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->find($targetId);

        if ($cannedResponse === null) {
            return response()->json([
                'success' => false,
                'message' => 'Template balasan cepat tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail template balasan cepat berhasil diambil.',
            'data' => $cannedResponse,
        ], 200);
    }

    /**
     * Memperbarui template balasan cepat.
     */
    public function update(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $targetId = $param2 !== null ? $param2 : $param1;

        $companyId = $this->resolveCompanyId($request, $tenant);

        $cannedResponse = CannedResponse::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->find($targetId);

        if ($cannedResponse === null) {
            return response()->json([
                'success' => false,
                'message' => 'Template balasan cepat tidak ditemukan.',
            ], 404);
        }

        // Format shortcut agar selalu berawalan '/' jika diisi
        if ($request->filled('shortcut')) {
            $shortcut = trim($request->input('shortcut'));
            if (! str_starts_with($shortcut, '/')) {
                $shortcut = '/'.$shortcut;
            }
            $request->merge(['shortcut' => $shortcut]);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'shortcut' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('canned_responses', 'shortcut')
                    ->where('company_id', $cannedResponse->company_id)
                    ->ignore($cannedResponse->id),
            ],
            'message' => 'sometimes|required|string',
            'department_id' => [
                'nullable',
                'integer',
                Rule::exists('departments', 'id')->where('company_id', $cannedResponse->company_id),
            ],
        ]);

        if (isset($validated['title'])) {
            $validated['title'] = trim($validated['title']);
        }

        if (array_key_exists('department_id', $validated) && empty($validated['department_id'])) {
            $validated['department_id'] = null;
        }

        $cannedResponse->update($validated);
        $cannedResponse->load(['company:id,name,slug', 'department:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Template balasan cepat berhasil diperbarui.',
            'data' => $cannedResponse,
        ], 200);
    }

    /**
     * Menghapus template balasan cepat.
     */
    public function destroy(Request $request, string $param1, ?string $param2 = null): JsonResponse
    {
        $tenant = $param2 !== null ? $param1 : null;
        $targetId = $param2 !== null ? $param2 : $param1;

        $companyId = $this->resolveCompanyId($request, $tenant);

        $cannedResponse = CannedResponse::query()
            ->when($companyId !== null, fn ($q) => $q->where('company_id', $companyId))
            ->find($targetId);

        if ($cannedResponse === null) {
            return response()->json([
                'success' => false,
                'message' => 'Template balasan cepat tidak ditemukan.',
            ], 404);
        }

        $cannedResponse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template balasan cepat berhasil dihapus.',
        ], 200);
    }

    /**
     * Menyelesaikan company_id dari tenant URL slug/ID atau query/body request.
     */
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
