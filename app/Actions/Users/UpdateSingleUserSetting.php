<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSingleUserSetting
{
    use AsAction;

    public function handle(User $user, string $settingName, mixed $value): void
    {
        $user->settings()->set($settingName, $value);
    }

    public function asController(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'setting' => 'required|string',
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $settingName = $request->input('setting');
        $value = $request->input('value');
        $this->handle($request->user(), $settingName, $value);

        return response()->json([
            'message' => 'User settings updated successfully.',
            'setting' => $settingName,
            'value' => $value,
        ]);
    }
}
