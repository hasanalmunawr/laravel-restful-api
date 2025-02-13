<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{

    public function create(int $idContact, AddressCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);

        $data = request()->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function list(int $idContact): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = Address::where('contact_id', $idContact)->get();

        return (new AddressResource($address))->response()->setStatusCode(200);
    }

    public function get(int $idAddress, int $idContact): AddressResource
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);

        return new AddressResource($address);
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request): AddressResource
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);

        $data = request()->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function delete(int $idAddress, int $idContact): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);
        $address->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

   private function getContact(User $user, int $idContact): Contact
   {
       $contact = Contact::where('user_id', $user->id)->where('id', $idContact)->first();
       if (!$contact) {
           throw new \HttpResponseException(response()->json([
               'errors' => [
                   "message" => [
                       "not found"
                   ]
               ]
           ])->setStatusCode(404));
       }
       return $contact;
   }

   private function getAddress(Contact $contact, int $idAddress): Address
   {
       $address = Address::where('contact_id', $contact->id)->where('id', $idAddress)->first();
       if (!$address) {
           throw new HttpResponseException(response()->json([
               'errors' => [
                   "message" => [
                       "not found"
                   ]
               ]
           ])->setStatusCode(404));
       }

       return $address;
   }
}
