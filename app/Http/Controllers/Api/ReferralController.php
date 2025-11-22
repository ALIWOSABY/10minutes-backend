<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ReferralController extends Controller
{
    /**
     * Get user's referral code and stats
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Generate referral code if doesn't exist
        $referralCode = $this->getUserReferralCode($user);

        // Get referral stats
        $totalReferrals = Referral::where('referrer_id', $user->id)->count();
        $completedReferrals = Referral::where('referrer_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $totalEarned = Referral::where('referrer_id', $user->id)
            ->sum('coins_earned');

        // Get recent referrals
        $recentReferrals = Referral::where('referrer_id', $user->id)
            ->with('referred')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'referral_code' => $referralCode,
                'referral_link' => url("/register?ref={$referralCode}"),
                'total_referrals' => $totalReferrals,
                'completed_referrals' => $completedReferrals,
                'total_earned' => $totalEarned,
                'recent_referrals' => $recentReferrals,
                'rewards' => [
                    'signup_reward' => setting('referral_signup_reward', 5),
                    'booking_reward' => setting('referral_booking_reward', 10),
                ],
            ]
        ]);
    }

    /**
     * Apply referral code during registration (called from AuthController)
     */
    public static function applyReferralCode($newUserId, $referralCode)
    {
        if (!$referralCode) {
            return;
        }

        // Find referrer by referral code
        $referrer = User::where('referral_code', $referralCode)->first();

        if (!$referrer) {
            return;
        }

        // Create referral record
        Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $newUserId,
            'referral_code' => $referralCode,
            'status' => 'pending',
        ]);

        // Give signup reward
        $signupReward = setting('referral_signup_reward', 5);

        DB::beginTransaction();
        try {
            $balanceBefore = $referrer->credit_balance;
            $referrer->increment('credit_balance', $signupReward);
            $referrer->refresh();

            // Create transaction
            $referrer->transactions()->create([
                'type' => 'credit',
                'amount' => $signupReward,
                'balance_before' => $balanceBefore,
                'balance_after' => $referrer->credit_balance,
                'source' => 'referral',
                'description' => 'Referral signup bonus',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Mark referral as completed (called when referred user makes first booking)
     */
    public static function completeReferral($userId)
    {
        $referral = Referral::where('referred_id', $userId)
            ->where('status', 'pending')
            ->first();

        if (!$referral) {
            return;
        }

        $referrer = User::find($referral->referrer_id);
        if (!$referrer) {
            return;
        }

        $bookingReward = setting('referral_booking_reward', 10);

        DB::beginTransaction();
        try {
            // Update referral status
            $referral->update([
                'status' => 'completed',
                'coins_earned' => $bookingReward,
                'completed_at' => now(),
            ]);

            // Give booking reward
            $balanceBefore = $referrer->credit_balance;
            $referrer->increment('credit_balance', $bookingReward);
            $referrer->refresh();

            // Create transaction
            $referrer->transactions()->create([
                'type' => 'credit',
                'amount' => $bookingReward,
                'balance_before' => $balanceBefore,
                'balance_after' => $referrer->credit_balance,
                'source' => 'referral',
                'description' => 'Referral booking bonus',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Get or create user's referral code
     */
    private function getUserReferralCode($user)
    {
        // Return existing code if already set
        if ($user->referral_code) {
            return $user->referral_code;
        }

        // Generate new unique code
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());

        // Save code to user
        $user->update(['referral_code' => $code]);

        return $code;
    }
}
