<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class OtpService
{
    public function generate(): string
    {
        return (string) rand(100000, 999999);
    }

    public function saveToUser(User $user, string $otp): void
    {
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);
    }

    public function verify(User $user, string $otp): bool
    {
        if ($user->otp !== $otp) return false;
        if ($user->isOtpExpired()) return false;
        return true;
    }

    public function markAsVerified(User $user): void
    {
        $user->update([
            'otp_verified_at' => Carbon::now(),
            'otp' => null,
            'otp_expires_at' => null,
        ]);
    }
}
