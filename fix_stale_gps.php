<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Fix GPS records yang persetujuannya tidak sinkron dengan status Pembayaran ===\n\n";

// GPS yang masih 'Diajukan ke Pembayaran' padahal pembayarannya sudah Ditolak
$stale = DB::table('gps_kendaraan as g')
    ->join('pembayarans as p', 'p.id', '=', 'g.pembayaran_id')
    ->where('g.persetujuan', 'Diajukan ke Pembayaran')
    ->where('p.status', 'Ditolak')
    ->select('g.id as gps_id', 'g.kendaraan_id', 'g.type', 'p.id as pembayaran_id', 'p.no_pr', 'p.status')
    ->get();

echo "Ditemukan " . count($stale) . " record GPS yang perlu diupdate ke 'Ditolak':\n";
foreach ($stale as $row) {
    echo "  GPS ID:{$row->gps_id} kendaraan:{$row->kendaraan_id} type:{$row->type} <- Pembayaran #{$row->pembayaran_id} ({$row->no_pr}) status:{$row->status}\n";
}

if (count($stale) > 0) {
    $ids = $stale->pluck('gps_id')->toArray();
    DB::table('gps_kendaraan')->whereIn('id', $ids)->update(['persetujuan' => 'Ditolak']);
    echo "\nSelesai. " . count($ids) . " record diupdate ke 'Ditolak'.\n";
} else {
    echo "\nTidak ada data yang perlu difix.\n";
}

// Juga cek GPS yang masih 'Diajukan ke Pembayaran' padahal pembayarannya Disetujui Sebagian tapi item itu ditolak
// Ini lebih kompleks - cek via source_data item_decisions
$partials = DB::table('pembayarans')
    ->where('status', 'Disetujui Sebagian')
    ->where('source_type', 'like', 'gps%')
    ->get();

$fixedPartial = 0;
foreach ($partials as $p) {
    $sd = json_decode($p->source_data, true);
    $itemDecisions = $sd['item_decisions'] ?? [];
    $recordIds = $sd['gps_record_ids'] ?? [];

    foreach ($itemDecisions as $decision) {
        $idx = $decision['idx'] ?? null;
        $action = $decision['action'] ?? null;
        if ($idx === null || $action !== 'rejected') continue;

        $recordId = $recordIds[$idx] ?? null;
        if (!$recordId) continue;

        $updated = DB::table('gps_kendaraan')
            ->where('id', $recordId)
            ->where('persetujuan', 'Diajukan ke Pembayaran')
            ->update(['persetujuan' => 'Ditolak']);

        if ($updated) {
            echo "Fixed partial: GPS ID:{$recordId} (Pembayaran #{$p->id} item idx:{$idx}) -> Ditolak\n";
            $fixedPartial++;
        }
    }
}

if ($fixedPartial > 0) {
    echo "\n{$fixedPartial} record partial fix.\n";
}
