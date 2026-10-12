<?php

namespace App\Services;

use App\Constants\NotificationType;
use App\Models\Member;
use App\Models\MembershipVerification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MemberService
{
    protected $activityLogService;
    protected $notificationService;

    public function __construct(
        ActivityLogService $activityLogService = null,
        NotificationService $notificationService = null
    ) {
        $this->activityLogService = $activityLogService ?? resolve(ActivityLogService::class);
        $this->notificationService = $notificationService ?? resolve(NotificationService::class);
    }

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

            $this->activityLogService->record(
                'member.verified',
                $member,
                "Verifikasi keanggotaan disetujui untuk " . ($member->user ? $member->user->name : "Anggota #{$member->id}") . ".",
                [
                    'member_id' => $member->id,
                    'status' => 'active',
                    'catatan' => $catatan,
                ],
                $adminId
            );

            if ($member->user_id) {
                $this->notificationService->send(
                    $member->user_id,
                    NotificationType::MEMBER_VERIFICATION_APPROVED,
                    'Verifikasi Keanggotaan Disetujui',
                    'Selamat, pengajuan verifikasi keanggotaan Anda telah disetujui.',
                    [
                        'member_id' => $member->id,
                    ]
                );
            }

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

            $this->activityLogService->record(
                'member.rejected',
                $member,
                "Verifikasi keanggotaan ditolak. Alasan: {$catatan}",
                [
                    'member_id' => $member->id,
                    'status' => 'rejected',
                    'rejection_reason' => $catatan,
                ],
                $adminId
            );

            if ($member->user_id) {
                $this->notificationService->send(
                    $member->user_id,
                    NotificationType::MEMBER_VERIFICATION_REJECTED,
                    'Verifikasi Keanggotaan Ditolak',
                    "Pengajuan verifikasi keanggotaan Anda ditolak. Alasan: {$catatan}",
                    [
                        'member_id' => $member->id,
                        'catatan' => $catatan,
                    ]
                );
            }

            return $member;
        });
    }

    public function resubmitMembership($memberId, array $data)
    {
        return DB::transaction(function () use ($memberId, $data) {
            $member = Member::lockForUpdate()->find($memberId);
            if (!$member) {
                throw new RuntimeException('Member tidak ditemukan.');
            }

            if ($member->status !== 'rejected') {
                throw new RuntimeException("Hanya anggota dengan status rejected yang dapat mengajukan ulang verifikasi.");
            }

            $resolvedName = $data['name'] ?? $data['nama'] ?? null;
            if (!empty($resolvedName) && $member->user) {
                $member->user->update([
                    'name' => $resolvedName,
                ]);
            }

            $member->nik = $data['nik'];
            $member->tanggal_lahir = $data['tanggal_lahir'];
            $member->alamat = $data['alamat'];
            $member->telepon = $data['telepon'];
            $member->pekerjaan = $data['pekerjaan'];
            $member->perusahaan = $data['perusahaan'] ?? '-';
            $member->status = 'pending';
            $member->save();

            $actorId = $member->user_id ?? (auth()->check() ? auth()->id() : null);

            $this->activityLogService->record(
                'member.resubmitted',
                $member,
                "Pengajuan ulang verifikasi profil anggota oleh " . ($member->user ? $member->user->name : "Anggota #{$member->id}") . ".",
                [
                    'member_id' => $member->id,
                    'status' => 'pending',
                ],
                $actorId
            );

            return $member;
        });
    }
}
