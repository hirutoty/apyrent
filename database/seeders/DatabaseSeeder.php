<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'username' => 'testuser'
            ],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('admin123'),

                'no_telp' => '08123456789',
                'foto' => null,

                'role' => 'superadmin',
                'status' => 'aktif',
            ]
        );

        $this->call([
            JenisSeeder::class,
            KendaraanSeeder::class,
            AnggaranProyekSeeder::class,
            JenisAsuransiSeeder::class,
            AsuransiSeeder::class,
            AsuransiKendaraanSeeder::class,
            PajakKendaraanSeeder::class,
            GpsSeeder::class,
            GpsKendaraanSeeder::class,
            SupplierSeeder::class,
            ServiceSeeder::class,
            ServiceHistorySeeder::class,
            ServiceDetailSeeder::class,
            KirSeeder::class,
            MemberSeeder::class,
            RentalSeeder::class,
            KeuanganSeeder::class,
            HutangVendorSeeder::class,
            BupotSeeder::class,
            RekonsiliasiBankSeeder::class,
            VirtualAccountSeeder::class,
            BukubesarSeeder::class,
            EfakturSeeder::class,
            LaporanKeuanganSeeder::class,
            SettingSeeder::class,
            ProcurementoSeeder::class,
            PurchaseroSeeder::class,
            VendoreoSeeder::class,
            // Sales Seeders
            CrmProspekSeeder::class,
            PenawaranSalesSeeder::class,
            SalesOrderSeeder::class,
            PricelistDiskonSeeder::class,
            TargetPenjualanSeeder::class,
            KomisiSalesSeeder::class,
            ReturPenjualanSeeder::class,
            SignatureDokumenSeeder::class,
            // Marketing Seeders
            KampanyeSeeder::class,
            OtomatisasiSeeder::class,
            SegmentasiSeeder::class,
            LoyaltySeeder::class,
            AfiliasiSeeder::class,
            SosmedpSeeder::class,
            TrackingutmSeeder::class,
            AdsIntegrationSeeder::class,
            // Project Seeders
            IndukProyekSeeder::class,
            ProjectPlanningSeeder::class,
            ProjectTimelineSeeder::class,
            ProjectCostSeeder::class,
            ProjectRiskSeeder::class,
            DokumenProyekSeeder::class,
            PembelianProyekSeeder::class,
            // Purchase
            RequestforQuotationSeeder::class,
            PurchaseOrderSeeder::class,
            VendorPricelistSeeder::class,
            ApprovalWorkflowSeeder::class,
            DropshippingSeeder::class,
            VendorPerformanceSeeder::class,
            // IT Technology
            ItTechnologySeeder::class,
        ]);
    }
}