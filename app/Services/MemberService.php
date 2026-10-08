<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MembershipVerification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MemberService
{
    public function approveMembership($memberId, $adminId, $catatan = null)
    {
        return DB::transaction(function () use ($memberId, $adminId, $catatan) {
            $member = Member::lockForUpdate()->find($memberId);
            if (!$member) {
                throw new RuntimeException('Member tidak ditemukan.');
            }

            if ($member->status !== 'pending') {
                throw new RuntimeException("Tidak dapat menyetujui member dengan status {$member->status}.");
            }

            $member->status = 'active';
            $member->save();

            MembershipVerification::create([
                'member_id' => $memberId,
                'admin_id' => $adminId,
                'status_keputusan' => 'approved',
                'catatan' => $catatan,
            ]);

            return $member;
        });
    }

    public function rejectMembership($memberId, $adminId, $catatan)
    {
        return DB::transaction(function () use ($memberId, $adminId, $catatan) {
            $member = Member::lockForUpdate()->find($memberId);
            if (!$member) {
                throw new RuntimeException('Member tidak ditemukan.');
            }

            if ($member->status !== 'pending') {
                throw new RuntimeException("Tidak dapat menolak member dengan status {$member->status}.");
            }

            if (empty($catatan)) {
                throw new RuntimeException('Catatan/alasan penolakan wajib diisi.');
            }

            $member->status = 'rejected';
            $member->save();

            MembershipVerification::create([
                'member_id' => $memberId,
                'admin_id' => $adminId,
                'status_keputusan' => 'rejected',
                'catatan' => $catatan,
            ]);

            return $member;
        });
    }
}
