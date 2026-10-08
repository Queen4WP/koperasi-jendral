<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResubmitMembershipRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Member;
use App\Services\MemberService;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    protected $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    public function show()
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            abort(403, 'Data anggota tidak ditemukan.');
        }

        $member->load(['latestVerification.admin', 'membershipVerifications.admin']);
        $latestRejection = $member->membershipVerifications()
            ->where('status_keputusan', 'rejected')
            ->latest()
            ->first();

        return view('profile.index', compact('user', 'member', 'latestRejection'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            abort(403, 'Data anggota tidak ditemukan.');
        }

        $resolvedName = $request->name ?? $request->nama;

        DB::transaction(function () use ($user, $member, $request, $resolvedName) {
            $user->update([
                'name' => $resolvedName,
            ]);

            $member->update([
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'pekerjaan' => $request->pekerjaan,
                'perusahaan' => $request->perusahaan ?? '-',
            ]);
        });

        return redirect()->route('member.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function resubmit(ResubmitMembershipRequest $request)
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            abort(403, 'Data anggota tidak ditemukan.');
        }

        if ($member->status !== 'rejected') {
            abort(403, 'Hanya anggota berstatus ditolak yang dapat mengajukan ulang verifikasi.');
        }

        $this->memberService->resubmitMembership($member->id, $request->validated());

        return redirect()->route('member.profile')->with('success', 'Data pendaftaran berhasil diperbarui dan diajukan ulang untuk verifikasi.');
    }

    public function resubmitMember(ResubmitMembershipRequest $request, Member $member)
    {
        if ($member->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak untuk mengubah data anggota ini.');
        }

        if ($member->status !== 'rejected') {
            abort(403, 'Hanya anggota berstatus ditolak yang dapat mengajukan ulang verifikasi.');
        }

        $this->memberService->resubmitMembership($member->id, $request->validated());

        return redirect()->route('member.profile')->with('success', 'Data pendaftaran berhasil diperbarui dan diajukan ulang untuk verifikasi.');
    }
}
