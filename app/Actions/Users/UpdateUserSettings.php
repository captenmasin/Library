<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUserSettings
{
    use AsAction;

    public function handle(User $user, array $settings): void
    {
        $user->settings()->setMultiple($settings);
    }

    public function asController(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $this->handle($request->user(), $request->input('settings'));

        return response()->json([
            'message' => 'User settings updated successfully.',
        ]);
    }
}
