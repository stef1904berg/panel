<?php

namespace App\Models;

use App\Enums\NetworkDriver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Network extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    public const RESOURCE_NAME = 'network';

    protected $guarded = ['id'];

    public static array $validationRules = [
        'name' => 'required|max:36|unique:networks,name',
        'driver' => 'required',
    ];

    protected $casts = [
        'driver' => NetworkDriver::class,
    ];

    /**
     * Gets information for the node associated with this server.
     */
    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }

    /**
     * Returns the network as an array
     */
    public function getNetwork()
    {
        return [
            'name' => $this->name,
            'driver' => $this->driver,
            'network_id' => $this->network_id,
        ];
    }
}
