<?php

namespace Faceless\Tenant\Services;

use Illuminate\Http\Request;
use Faceless\Tenant\Models\Tenant as TenantModel;
use Faceless\Tenant\Models\TenantAddress as Address;
use Faceless\Tenant\Models\TenantContact as Contact;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Estox\JsonResponse;
use Faceless\Tenant\Resources\TenantResource;
use Faceless\Extensions\Models\Extension as ExtensionModel;

trait Tenant
{

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function browse(Request $request, $limitter = 10)
  {
    $tenant = TenantModel::where('user_id', auth('api')->user()->id)->orderBy('subscribed', 'DESC')->get();
    return TenantResource::collection($tenant);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function read(TenantModel $tenant)
  {
    return new TenantResource($tenant);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(Request $request, TenantModel $tenant)
  {
    if (!empty($request->file('picture'))) {
      $status = $this->pictureUpload($request, $tenant);
      if (!$status['status']) {
        return response()->json(['message' => $status['message'], 'code' => 403]);
      }
      return response()->json(['code' => 0, 'message' => 'Success']);
    }
    if ($tenant === null) {
      return redirect()->back()->with('failure', 'Store Service not found');
    }
    $tenant->name = $request->name;
    $tenant->slogan = $request->slogan;
    $tenant->color = $request->color;
    $tenant->description = $request->description;
    $tenant->save();
    return new TenantResource($tenant);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function add(Request $request)
  {
    $tenant = new TenantModel;
    $tenant->name = $request->name;
    $tenant->slogan = $request->slogan;
    $tenant->subscribed = true;
    $tenant->color = $request->color;
    $tenant->description = $request->description;
    $tenant->user_id = $request->user()->id;
    $tenant->save();

    if (!empty($request->file('logo'))) {
      $status = $this->logoUpload($request, $tenant);
      if (!$status['status']) {
        return response()->json(['message' => $status['message'], 'code' => 403]);
      }
    }
    return response()->json(['code' => 0, 'message' => 'Success']);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }

  /**
   * subscription
   */
  public function subscription(Request $request, TenantModel $tenant)
  {
    if (empty($tenant)) {
      return response()->json(['message' => 'Tenant subscription not found', 'code' => 404]);
    }
    $tenant->subscribed = $request->is_subscribed ? false : true;
    $tenant->save();
    return new TenantResource($tenant);
  }

  /**
   * Address
   */
  public function address(Request $request, TenantModel $tenant)
  {
    try {
      if (empty($tenant)) {
        return response()->json(new JsonResponse(['code' => Response::HTTP_NOT_FOUND, 'errors' => "Could not find business"], 'Business Address Error'), Response::HTTP_NOT_FOUND);
      }

      $validator = Validator::make($request->all(), [
        'line1' => 'required|string',
        'city' => 'required|string',
        'zipcode' => 'required|string',
        'country' => 'required|string'
      ]);

      if ($validator->fails()) {
        return response()->json(new JsonResponse(['code' => Response::HTTP_UNPROCESSABLE_ENTITY, 'errors' => $validator->errors()->all()], 'Business Address Error'), Response::HTTP_UNPROCESSABLE_ENTITY);
      }

      if (empty($tenant->address)) {
        $address = new Address;
        $address->line1 = $request->line1;
        $address->line2 = empty($request->line2) ? null : $request->line2;
        $address->suburb = empty($request->suburb) ? null : $request->suburb;
        $address->city = $request->city;
        $address->zipcode = $request->zipcode;
        $address->country = $request->country;
        $address->tenant_id = $tenant->id;
        $address->save();
        return new TenantResource($tenant);
      } else {
        $address = Address::find($tenant->id);
        $address->line1 = $request->line1;
        $address->line2 = empty($request->line2) ? null : $request->line2;
        $address->suburb = empty($request->suburb) ? null : $request->suburb;
        $address->city = $request->city;
        $address->zipcode = $request->zipcode;
        $address->country = $request->country;
        $address->tenant_id = $tenant->id;
        $address->save();
        return new TenantResource($tenant);
      }
    } catch (\Throwable $th) {
      return response()->json(new JsonResponse(['code' => Response::HTTP_NOT_IMPLEMENTED, 'errors' => $th], 'Business Address Error'), Response::HTTP_NOT_FOUND);
    }
  }

  /**
   * Contact
   */
  public function contact(Request $request, TenantModel $tenant)
  {
    try {
      if (empty($tenant)) {
        return response()->json(new JsonResponse(['code' => Response::HTTP_NOT_FOUND, 'errors' => "Could not find business"], 'Business Contact Error'), Response::HTTP_NOT_FOUND);
      }
      if (empty($tenant->contact)) {
        $contact = new Contact;
        $contact->fax = empty($request->fax) ? null : $request->fax;
        $contact->telephone = empty($request->telephone) ? null : $request->telephone;
        $contact->email = empty($request->email) ? null : $request->email;
        $contact->other = empty($request->other) ? null : $request->other;
        $contact->tenant_id = $tenant->id;
        $contact->save();
        return new TenantResource($tenant);
      } else {
        $contact = Contact::find($tenant->id);
        $contact->fax = empty($request->fax) ? null : $request->fax;
        $contact->telephone = empty($request->telephone) ? null : $request->telephone;
        $contact->email = empty($request->email) ? null : $request->email;
        $contact->other = empty($request->other) ? null : $request->other;
        $contact->tenant_id = $tenant->id;
        $contact->save();
        return new TenantResource($tenant);
      }
    } catch (\Throwable $th) {
      return response()->json(new JsonResponse(['code' => Response::HTTP_NOT_IMPLEMENTED, 'errors' => $th], 'Business Contact Error'), Response::HTTP_NOT_FOUND);
    }
  }

  /**
   * Upload avatar for employee
   * Spatie
   * @return array
   */
  private function logoUpload(Request $request, TenantModel $tenant)
  {
    try {
      $file = $request->file('logo');
      // $employee->last()->delete();
      $tenant->addMedia($file)
        ->usingName('logo')
        ->usingFileName('logo.' . $file->getClientOriginalExtension())
        ->withCustomProperties(['type' => 'logo'])
        ->toMediaCollection('logo');

      return ['status' => true];
    } catch (\Throwable $th) {
      return ['status' => false, 'message' => 'Allowed image formats(.png, jpeg)'];
    }
  }

  /**
   * Add Modules/Extensions to Tenant
   */
  public function attach_extension(TenantModel $tenant, ExtensionModel $extension) {
    $tenant->extensions()->attach($extension->id, ['is_active' => true]);
    return response()->json(['code' => 0, 'message' => 'Success']);
  }
}
