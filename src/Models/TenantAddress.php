<?php

namespace Faceless\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantAddress extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    protected $fillable = [
        'line1', 'line2', 'suburb', 'city', 'zipcode', 'country', 'tenant_id'
    ];
    /**
     * Get the tenant that address.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
