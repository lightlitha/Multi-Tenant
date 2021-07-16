<?php

namespace Faceless\Tenant\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {
    return [
      'id'    => $this->id,
      'name' => $this->name,
      'slogan' => $this->slogan,
      'color' => $this->color,
      'logo' => 'https://picsum.photos/200',
      'is_subscribed' => $this->subscribed,
      'description' => $this->description,
      'address' => $this->address,
      'contact' => $this->contact,
      'extensions' => $this->extensions
    ];
  }
}
