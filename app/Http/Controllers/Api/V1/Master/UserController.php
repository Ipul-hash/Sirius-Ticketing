<?php

namespace App\Http\Controllers\Api\V1\Master;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar staf/pengguna dengan filter dan paginasi.
     */
    public function index(Request $request, ?string $tenant = null): JsonResponse
    {
        $companyId = $this->resolveCompanyId($request, $tenant);

        $query = User::query()
            ->with(['company:id,name,slug', 'department:id,name'])
            ->withCount(['assignedTickets', 'requestedTickets']);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('job_title', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->query('department_id'));
        }

        if ($request->has('is_active') && $request->query('is_active') !== '') {
            $isActive = filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN);
            $query->where('is_active', $isActive);
        }

        $sortBy = $request->query('sort_by', 'name');
        $sortOrder = $request->query('sort_order', 'asc');
        if (in_array($sortBy, ['id', 'name', 'email', 'role', 'created_at'], true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        if ($request->query('paginate') === 'false') {
            $users = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar semua pengguna berhasil diambil.',
                'data' => $users,
            ], 200);
        }

        $perPage = (int) $request->query('per_page', 10);
        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar pengguna berhasil diambil.',
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ], 200);
    }

    /**
     * Menyimpan data staf/pengguna baru ke dalam sistem.
     */
    public function store(Request $request, ?string $tenant = null): JsonResponse
    {
        $authUser = $request->user();
        $companyId = $this->resolveCompanyId($request, $tenant);
        $isSuperadmin = $authUser?->isSuperadmin() ?? false;

        $allowedRoles = [UserRole::CompanyAdmin->value, UserRole::Agent->value, UserRole::Requester->value];
        if ($isSuperadmin) {
            $allowedRoles[] = UserRole::Superadmin->value;
        }

        $validated = $request->validate([
            'company_id' => [
                $companyId !== null ? 'nullable' : 'required',
                'exists:companies,id',
            ],
            'department_id' => [
                'nullable',
                Rule::exists('departments', 'id')->where(function ($query) use ($companyId, $request) {
                    $targetCompanyId = $companyId ?? $request->input('company_id');
                    if ($targetCompanyId !== null) {
                        $query->where('company_id', $targetCompanyId);
                    }
                }),
            ],
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6|max:100',
            'role' => ['required', Rule::in($allowedRoles)],
            'job_title' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'is_active' => 'sometimes|boolean',
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email pengguna wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'role.required' => 'Peran (role) pengguna wajib dipilih.',
            'role.in' => 'Peran (role) tidak valid atau Anda tidak memiliki izin untuk memilih peran ini.',
            'department_id.exists' => 'Departemen tidak valid atau tidak termasuk dalam organisasi perusahaan.',
        ]);

        if ($companyId !== null) {
            $validated['company_id'] = $companyId;
        }

        if (! isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->load(['company:id,name,slug', 'department:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna staf berhasil ditambahkan.',
            'data' => $user,
        ], 201);
    }

    /**
     * Mengambil data detail satu staf/pengguna.
     */
    public function show(Request $request, int|string $id, ?string $tenant = null): JsonResponse
    {
        $authUser = $request->user();
        $isSuperadmin = $authUser?->isSuperadmin() ?? false;
        $companyId = $this->resolveCompanyId($request, $tenant);

        $user = User::with(['company:id,name,slug', 'department:id,name'])
            ->withCount(['assignedTickets', 'requestedTickets'])
            ->find($id);

        if ($user === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        if (! $isSuperadmin && $companyId !== null && (int) $user->company_id !== (int) $companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Pengguna ini berada di luar organisasi Anda.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail pengguna berhasil diambil.',
            'data' => $user,
        ], 200);
    }

    /**
     * Memperbarui data staf/pengguna.
     */
    public function update(Request $request, int|string $id, ?string $tenant = null): JsonResponse
    {
        $authUser = $request->user();
        $isSuperadmin = $authUser?->isSuperadmin() ?? false;
        $companyId = $this->resolveCompanyId($request, $tenant);

        $user = User::find($id);

        if ($user === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        if (! $isSuperadmin && $companyId !== null && (int) $user->company_id !== (int) $companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak berwenang mengedit pengguna organisasi lain.',
            ], 403);
        }

        $targetCompanyId = $user->company_id ?? $companyId;

        $allowedRoles = [UserRole::CompanyAdmin->value, UserRole::Agent->value, UserRole::Requester->value];
        if ($isSuperadmin) {
            $allowedRoles[] = UserRole::Superadmin->value;
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:6|max:100',
            'role' => ['required', Rule::in($allowedRoles)],
            'department_id' => [
                'nullable',
                Rule::exists('departments', 'id')->where(function ($query) use ($targetCompanyId) {
                    if ($targetCompanyId !== null) {
                        $query->where('company_id', $targetCompanyId);
                    }
                }),
            ],
            'job_title' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'is_active' => 'sometimes|boolean',
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email pengguna wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.min' => 'Password baru minimal harus 6 karakter.',
            'role.required' => 'Peran (role) pengguna wajib dipilih.',
            'role.in' => 'Peran (role) tidak valid atau Anda tidak memiliki izin untuk memilih peran ini.',
            'department_id.exists' => 'Departemen tidak valid atau bukan bagian dari organisasi perusahaan ini.',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        $user->load(['company:id,name,slug', 'department:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil diperbarui.',
            'data' => $user,
        ], 200);
    }

    /**
     * Menghapus staf/pengguna atau menolak jika memiliki riwayat tiket.
     */
    public function destroy(Request $request, int|string $id, ?string $tenant = null): JsonResponse
    {
        $authUser = $request->user();
        $isSuperadmin = $authUser?->isSuperadmin() ?? false;
        $companyId = $this->resolveCompanyId($request, $tenant);

        $user = User::find($id);

        if ($user === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        if (! $isSuperadmin && $companyId !== null && (int) $user->company_id !== (int) $companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak berwenang menghapus pengguna organisasi lain.',
            ], 403);
        }

        if ($authUser !== null && (int) $authUser->id === (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.',
            ], 422);
        }

        if ($user->requestedTickets()->exists() || $user->assignedTickets()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak dapat dihapus karena memiliki riwayat tiket (sebagai pemohon atau teknisi yang ditugaskan). Nonaktifkan status akun sebagai gantinya.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus.',
        ], 200);
    }

    /**
     * Menyelesaikan target ID Perusahaan berdasarkan sesi user, token, atau rute tenant.
     */
    protected function resolveCompanyId(Request $request, ?string $tenant = null): ?int
    {
        $user = $request->user();
        if ($user !== null && ! $user->isSuperadmin() && $user->company_id !== null) {
            return (int) $user->company_id;
        }

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

        if ($user !== null && $user->company_id !== null) {
            return (int) $user->company_id;
        }

        return null;
    }
}
