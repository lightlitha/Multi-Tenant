<?php

namespace Faceless\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantContact extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'email', 'telephone', 'fax', 'other', 'tenant_id'
    ];

    /**
     * Get the tenant that owns.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
