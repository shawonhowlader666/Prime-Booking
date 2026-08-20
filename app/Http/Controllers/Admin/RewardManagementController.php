<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardPayoutRequest;
use App\Models\UserReward;
use App\Models\SiteSetting;
use App\Services\RewardPointService;
use Illuminate\Http\Request;

class RewardManagementController extends Controller
{
    public function index(Request $request, RewardPointService $rewardService)
    {
        $status = $request->input('status', 'all');
        $query = RewardPayoutRequest::with('user')->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $payouts = $query->paginate(15);

        $stats = [
            'total_points_in_circulation' => (int) UserReward::sum('points_balance'),
            'total_points_redeemed'       => (int) UserReward::sum('total_redeemed_points'),
            'pending_payouts_count'       => RewardPayoutRequest::where('status', 'pending')->count(),
            'pending_payouts_amount'      => (float) RewardPayoutRequest::where('status', 'pending')->sum('amount'),
            'approved_payouts_amount'     => (float) RewardPayoutRequest::where('status', 'approved')->sum('amount'),
            'spend_per_point'             => $rewardService->getSpendPerPoint(),
            'point_value_bdt'             => $rewardService->getPointValueInBdt(),
            'min_redemption_points'       => $rewardService->getMinRedemptionPoints(),
        ];

        return view('admin.rewards.index', compact('payouts', 'stats', 'status'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'reward_spend_per_point'        => 'required|numeric|min:1',
            'reward_point_value_bdt'        => 'required|numeric|min:0.1',
            'reward_min_redemption_points'  => 'required|integer|min:1',
        ]);

        SiteSetting::set('reward_spend_per_point', $request->reward_spend_per_point);
        SiteSetting::set('reward_point_value_bdt', $request->reward_point_value_bdt);
        SiteSetting::set('reward_min_redemption_points', $request->reward_min_redemption_points);

        return back()->with('success', '✅ Reward loyalty rules updated successfully!');
    }

    public function approvePayout(Request $request, $id, RewardPointService $rewardService)
    {
        $payout = RewardPayoutRequest::findOrFail($id);
        $rewardService->approvePayout($payout, $request->input('admin_note', 'Approved and paid by Admin'));

        return back()->with('success', "✅ Payout #{$id} of ৳{$payout->amount} approved successfully!");
    }

    public function rejectPayout(Request $request, $id, RewardPointService $rewardService)
    {
        $payout = RewardPayoutRequest::findOrFail($id);
        $reason = $request->input('admin_note', 'Account details invalid or unverified');
        $rewardService->rejectPayout($payout, $reason);

        return back()->with('success', "❌ Payout #{$id} rejected and {$payout->points} Points refunded to user.");
    }
}
