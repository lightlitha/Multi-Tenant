<?php

namespace Faceless\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Tenant extends Model implements HasMedia
{
  use HasFactory, HasMediaTrait;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'slogan', 'color', 'subscribed', 'description', 'user_id',
  ];

  /**
   * Media Collection
   * Spatie
   */
  public function registerMediaCollections(): void
  {
    $this->addMediaCollection('logo')->singleFile();
  }

  /**
   * Media Conversions
   * Spatie
   */
  public function registerMediaConversions(Media $media = null)
  {
    $this->addMediaConversion('thumb')
      ->width(60)
      ->height(60)
      ->sharpen(10);
  }

  /**
   * Get the address record associated with the tenant.
   */
  public function address()
  {
    return $this->hasOne(TenantAddress::class);
  }

  /**
   * Get the address record associated with the tenant.
   */
  public function contact()
  {
    return $this->hasOne(TenantContact::class);
  }

  /**
   * Get the user record associated with the tenant.
   */
  public function user()
  {
    return $this->belongsTo(\App\Models\User::class);
  }

  /**
   * Get the tenant extensions.
   */
  public function extensions()
  {
    return $this->belongsToMany(\Faceless\Extensions\Models\Extension::class)->using(\Faceless\Extensions\Models\ExtensionTenant::class)->withPivot(['is_active']);
  }
}
