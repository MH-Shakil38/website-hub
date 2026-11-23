<?php

namespace App\Http\Middleware;

use App\Models\Reseller;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class TrackResellerReferral
{
    public const COOKIE_NAME = 'saas_referral_code';

    public function handle(Request $request, Closure $next): Response
    {
        $refCode = trim((string) $request->query('ref', ''));
        if ($refCode !== '') {
            try {
                if (Schema::connection('central')->hasTable('core_resellers')) {
                    $reseller = Reseller::where('referral_code', strtoupper($refCode))->first();
                    if ($reseller) {
                        // Self-referral check: do not track if the logged-in user IS this reseller
                        if (!auth()->check() || (int) auth()->id() !== (int) $reseller->user_id) {
                            $reseller->increment('referral_clicks');
                            session([
                                'saas_referral_code' => $reseller->referral_code,
                                'saas_referral_reseller_user_id' => $reseller->user_id,
                            ]);
                            // Queue 30-day referral cookie (43200 minutes)
                            Cookie::queue(self::COOKIE_NAME, $reseller->referral_code, 43200);
                        }
                    }
                }
            } catch (\Throwable) {
            }
        }

        return $next($request);
    }

    /**
     * Resolve the referred reseller User ID from current session or 30-day cookie,
     * while preventing self-referral if $newUserEmail matches the reseller's email.
     */
    public static function resolveReferredResellerUserId(?Request $request = null, ?string $newUserEmail = null): ?int
    {
        $request = $request ?? request();
        $code = session('saas_referral_code') ?: $request->cookie(self::COOKIE_NAME);

        if (!$code) {
            return null;
        }

        try {
            $reseller = Reseller::with('user')->where('referral_code', strtoupper((string) $code))->first();
            if (!$reseller || !$reseller->user) {
                return null;
            }

            // Prevent self-referral
            if ($newUserEmail && strcasecmp($reseller->user->email, $newUserEmail) === 0) {
                return null;
            }
            if (auth()->check() && (int) auth()->id() === (int) $reseller->user_id) {
                return null;
            }

            return (int) $reseller->user_id;
        } catch (\Throwable) {
            return null;
        }
    }
}
