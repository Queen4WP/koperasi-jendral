<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewMembershipRequest;
use App\Models\Member;
use App\Services\MemberService;
use Exception;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    protected $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    public function index(Request $request)
    {
        $query = Member::with(['user', 'latestVerification.admin'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $members = $query->paginate(15)->withQueryString();
        $status = $request->status;
        $search = $request->search;

        return view('admin.members.index', compact('members', 'status', 'search'));
    }

    public function show(Member $member)
    {
        $member->load(['user', 'membershipVerifications.admin']);
        $verifications = $member->membershipVerifications()->with('admin')->latest()->get();
        $latestVerification = $member->latestVerification;

        return view('admin.members.show', compact('member', 'verifications', 'latestVerification'));
    }

    public function showReview(Member $member)
    {
        $member->load(['user', 'membershipVerifications.admin']);
        return view('admin.members.review', compact('member'));
    }

    public function review(ReviewMembershipRequest $request, Member $member)
    {
        try {
            $adminId = auth()->id();
            
            if ($request->action === 'approve') {
                $this->memberService->approveMembership($member->id, $adminId, $request->catatan);
                return redirect()->back()->with('success', 'Keanggotaan berhasil disetujui (Approved).');
            } else {
                $this->memberService->rejectMembership($member->id, $adminId, $request->catatan);
                return redirect()->back()->with('success', 'Keanggotaan berhasil ditolak (Rejected).');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
