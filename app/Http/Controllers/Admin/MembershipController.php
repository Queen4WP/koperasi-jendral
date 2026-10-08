<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewMembershipRequest;
use App\Models\Member;
use App\Services\MemberService;
use Exception;

class MembershipController extends Controller
{
    protected $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    public function showReview(Member $member)
    {
        $member->load('user');
        return view('admin.members.review', compact('member'));
    }

    public function review(ReviewMembershipRequest $request, $memberId)
    {
        try {
            $adminId = auth()->id();
            
            if ($request->action === 'approve') {
                $this->memberService->approveMembership($memberId, $adminId, $request->catatan);
                return redirect()->back()->with('success', 'Membership approved.');
            } else {
                $this->memberService->rejectMembership($memberId, $adminId, $request->catatan);
                return redirect()->back()->with('success', 'Membership rejected.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
