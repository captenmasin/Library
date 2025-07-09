<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'username' => $this->username,
            'avatar' => $this->avatar,
            'email_verified' => $this->email_verified_at !== null,
        ];
    }

    public function asUser(): array
    {
        $data = $this->toArray(request());

        $data['email'] = $this->email;
        $data['settings'] = $this->settings()->all();
        $data['permissions'] = $this->getAllPermissions()->pluck('name')->toArray();
        $data['book_identifiers'] = $this->getBookIdentifiers();

        return $data;
    }
}
