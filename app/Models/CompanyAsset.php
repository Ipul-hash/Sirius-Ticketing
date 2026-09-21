<?php

namespace App\Models;

use App\Enums\AssetCategory;
use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyAsset extends Model
{
    /**
     * @var string
     */
    protected $table = 'company_assets';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'department_id',
        'assigned_to_user_id',
        'asset_tag',
        'name',
        'category',
        'serial_number',
        'status',
        'notes',
    ];

    /**
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'category' => AssetCategory::class,
            'status' => AssetStatus::class,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'asset_id');
    }
}
