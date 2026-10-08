<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            abort(403, 'Data anggota tidak ditemukan.');
        }

        return view('profile.index', compact('user', 'member'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            abort(403, 'Data anggota tidak ditemukan.');
        }

        DB::transaction(function () use ($user, $member, $request) {
            $user->update([
                'name' => $request->name,
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
}
