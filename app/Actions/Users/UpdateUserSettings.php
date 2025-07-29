<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Actions\TrackEvent;
use Illuminate\Http\Request;
use App\Enums\AnalyticsEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUserSettings
{
    use AsAction;

    public function handle(User $user, array $settings): void
    {
        TrackEvent::dispatchAfterResponse(AnalyticsEvent::MultipleSettingsUpdated, [
            'user_id' => $user->id,
            'settings' => json_encode($settings),
        ]);

        $user->settings()->setMultiple($settings);
    }

    public function asController(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors(),
            ], 422);
        }

        $this->handle($request->user(), $request->input('settings'));

        return response()->json([
            'success' => true,
            'message' => 'User settings updated successfully.',
        ]);
    }
}
