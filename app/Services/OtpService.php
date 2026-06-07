<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class OtpService
{
    public function generateAndSend(User $user, $customOtp = null): string
    {
        $otp = $customOtp ?? (string) rand(100000, 999999);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);


        //  WA FONNTE 

        /*
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $user->no_hp,
                'message' => "*Apotek Kinara Pharma*\n\nKode OTP Reset Password Anda adalah: *$otp*\n\nMohon jangan berikan kode ini kepada siapapun.",
                'countryCode' => '62', // Otomatis kode negara Indonesia
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: TOKEN_FONNTE_LU_DISINI'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        */

        return $otp;
    }

    public function verify(User $user, string $otp): bool
    {
        // Cek OTP 
        if ($user->otp !== $otp) {
            return false;
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return false;
        }

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
