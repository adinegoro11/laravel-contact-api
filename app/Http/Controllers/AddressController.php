<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $idContact): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $addresses = Address::where('contact_id', $contact->id)->get();
        return (AddressResource::collection($addresses))->response()->setStatusCode(200);
    }

    private function getContact(User $user, int $idContact): Contact
    {
        $contact = Contact::where('id', $idContact)->where('user_id', $user->id)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'not found'
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
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }
        return $address;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(int $idContact, AddressCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();
        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $idContact, int $idAddress): AddressResource
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);
        return new AddressResource($address);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request): AddressResource
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);

        $data = $request->validated();
        $address->fill($data);
        $address->save();
        return new AddressResource($address);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $idContact, int $idAddress): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);
        $address->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
