<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CannedResponse extends Model
{
    /**
     * @var string
     */
    protected $table = 'canned_responses';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'department_id',
        'title',
        'shortcut',
        'message',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
