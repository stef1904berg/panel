<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServerNetwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_id',
        'network_id',
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function network(): BelongsTo
    {
        return $this->belongsTo(Network::class);
    }
}
