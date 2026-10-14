@props(['status'])

@php
$badges = [
    'pending'      => ['class' => 'badge-warning', 'label' => 'Menunggu Verifikasi'],
    'approved'     => ['class' => 'badge-success', 'label' => 'Disetujui'],
    'rejected'     => ['class' => 'badge-danger',  'label' => 'Ditolak'],
    
    'submitted'    => ['class' => 'badge-info',    'label' => 'Diajukan'],
    'under_review' => ['class' => 'badge-warning', 'label' => 'Sedang Ditinjau'],
    'disbursed'    => ['class' => 'badge-primary', 'label' => 'Dicairkan'],
    'active'       => ['class' => 'badge-success', 'label' => 'Aktif'],
    'paid_off'     => ['class' => 'badge-secondary','label' => 'Lunas'],
    
    'unpaid'       => ['class' => 'badge-warning', 'label' => 'Belum Dibayar'],
    'partial'      => ['class' => 'badge-info',    'label' => 'Sebagian / Cicilan'],
    'paid'         => ['class' => 'badge-success', 'label' => 'Lunas'],
    'overdue'      => ['class' => 'badge-danger',  'label' => 'Terlambat'],
    'failed'       => ['class' => 'badge-danger',  'label' => 'Ditolak'],
    'success'      => ['class' => 'badge-success', 'label' => 'Berhasil'],

    // Status Verifikasi Dokumen
    'sesuai'       => ['class' => 'badge-success', 'label' => 'Sesuai'],
    'tidak_sesuai' => ['class' => 'badge-danger',  'label' => 'Tidak Sesuai'],
];

$badge = $badges[$status] ?? ['class' => 'badge-secondary', 'label' => ucfirst($status)];
@endphp

<span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
