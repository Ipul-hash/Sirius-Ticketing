<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAttachment extends Model
{
    public const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'ticket_attachments';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'ticket_id',
        'ticket_message_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'file_size_kb',
        'file_type',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_size_kb' => 'integer',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(TicketMessage::class, 'ticket_message_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
