<?php

namespace App\Models;

use App\Enums\TicketApprovalStatus;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'ticket_number',
        'subject',
        'description',
        'category_id',
        'department_id',
        'requester_id',
        'assigned_to',
        'asset_id',
        'status',
        'priority',
        'approval_status',
        'is_merged',
        'merged_into_ticket_id',
        'first_response_due_at',
        'first_responded_at',
        'resolution_due_at',
        'resolved_at',
        'closed_at',
        'is_sla_breached',
        'last_reply_at',
        'replies_count',
        'satisfaction_rating',
        'satisfaction_feedback',
    ];

    /**
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'priority' => TicketPriority::class,
            'approval_status' => TicketApprovalStatus::class,
            'is_merged' => 'boolean',
            'is_sla_breached' => 'boolean',
            'replies_count' => 'integer',
            'satisfaction_rating' => 'integer',
            'first_response_due_at' => 'datetime',
            'first_responded_at' => 'datetime',
            'resolution_due_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'last_reply_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(CompanyAsset::class, 'asset_id');
    }

    public function mergedInto(): BelongsTo
    {
        return $this->belongsTo(self::class, 'merged_into_ticket_id');
    }

    public function mergedIntoTicket(): BelongsTo
    {
        return $this->mergedInto();
    }

    public function mergedTickets(): HasMany
    {
        return $this->hasMany(self::class, 'merged_into_ticket_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(TicketApproval::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class);
    }

    public function collisions(): HasMany
    {
        return $this->hasMany(TicketCollision::class);
    }
}
