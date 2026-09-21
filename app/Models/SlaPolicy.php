<?php

namespace App\Models;

use App\Enums\TicketPriority;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaPolicy extends Model
{
    /**
     * @var string
     */
    protected $table = 'sla_policies';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'priority',
        'first_response_time_minutes',
        'resolution_time_minutes',
    ];

    /**
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'priority' => TicketPriority::class,
            'first_response_time_minutes' => 'integer',
            'resolution_time_minutes' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
