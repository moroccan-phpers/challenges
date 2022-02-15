<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $data)
 * @method static findOrFail(string $id)
 * @method static where(string[] $array)
 * @property string $sender
 * @property string $recipient
 * @property string $message
 * @property string $request_id
 */
class EmailRequest extends Model
{
    use HasFactory, Uuids;

    const SENT = 'sent';
    const FAILED = 'failed';
    const PROCESSING = 'processing';

    protected $fillable = [
        'sender',
        'recipient',
        'message',
        'status',
    ];
}
