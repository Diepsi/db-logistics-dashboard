<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Mengisi koordinat (latitude/longitude) untuk lokasi yang sudah ada di database.
 * Pencocokan dilakukan pada nama kota/kabupaten setelah normalisasi
 * (buang prefiks "Kab." / "Kota", lowercase, rapikan spasi) sehingga
 * toleran terhadap variasi penulisan antar file import.
 */
class LocationCoordinateSeeder extends Seeder
{
    /**
     * @var array<string, array{0: float, 1: float}>
     */
    public const COORDINATES = [
        'banda aceh' => [5.5482900, 95.3238000],
        'lhokseumawe' => [5.1128000, 97.1471000],
        'subulussalam' => [2.6333000, 97.9833000],
        'medan' => [3.5952000, 98.6722000],
        'padang sidimpuan' => [1.3748000, 99.2719000],
        'sibolga' => [1.7406000, 98.7797000],
        'tanjung balai' => [2.9667000, 99.8000000],
        'gunungsitoli' => [1.2883000, 97.6203000],
        'pekanbaru' => [0.5071000, 101.4478000],
        'jambi' => [-1.6101000, 103.6131000],
        'bengkulu' => [-3.8004000, 102.2655000],
        'bandar lampung' => [-5.4292000, 105.2611000],
        'sawah lunto' => [-0.6667000, 101.4167000],
        'jakarta pusat' => [-6.1862000, 106.8342000],
        'jakarta barat' => [-6.1667000, 106.7889000],
        'jakarta selatan' => [-6.2615000, 106.8106000],
        'jakarta timur' => [-6.2449000, 106.8790000],
        'jakarta utara' => [-6.1383000, 106.8667000],
        'bogor' => [-6.5950000, 106.8166000],
        'depok' => [-6.4025000, 106.7942000],
        'bekasi' => [-6.2383000, 106.9756000],
        'cimahi' => [-6.8915000, 107.5372000],
        'bandung' => [-6.9175000, 107.6191000],
        'cirebon' => [-6.7320000, 108.5523000],
        'sukabumi' => [-6.9281000, 106.9302000],
        'garut' => [-7.1097000, 107.7697000],
        'cianjur' => [-6.8167000, 107.1333000],
        'indramayu' => [-6.3276000, 108.3333000],
        'majalengka' => [-6.7642000, 108.2139000],
        'sumedang' => [-6.8500000, 107.9167000],
        'subang' => [-6.5667000, 107.7667000],
        'purwakarta' => [-6.5569000, 107.4433000],
        'karawang' => [-6.3011000, 107.3039000],
        'kuningan' => [-6.9758000, 108.4831000],
        'ciamis' => [-7.2747000, 108.3542000],
        'pangandaran' => [-7.6833000, 108.6333000],
        'batang' => [-6.9069000, 109.7253000],
        'pekalongan' => [-6.8886000, 109.6753000],
        'rembang' => [-6.7089000, 111.3449000],
        'purbalingga' => [-7.3881000, 109.3642000],
        'brebes' => [-6.8722000, 109.0400000],
        'jepara' => [-6.5894000, 110.6689000],
        'kudus' => [-6.8081000, 110.8386000],
        'tegal' => [-6.8797000, 109.1256000],
        'cilacap' => [-7.7240000, 109.0172000],
        'sukoharjo' => [-7.6747000, 110.8283000],
        'karanganyar' => [-7.6458000, 110.9431000],
        'wonogiri' => [-7.7072000, 110.9225000],
        'semarang' => [-7.0051000, 110.4387000],
        'temanggung' => [-7.5508000, 110.1750000],
        'wonosobo' => [-7.3631000, 109.9025000],
        'kebumen' => [-7.6706000, 109.6528000],
        'sragen' => [-7.4264000, 110.8947000],
        'demak' => [-6.8861000, 110.6394000],
        'kendal' => [-6.9411000, 110.2064000],
        'bantul' => [-7.8886000, 110.3284000],
        'gunung kidul' => [-8.0586000, 110.6036000],
        'malang' => [-7.9666000, 112.6326000],
        'surabaya' => [-7.2575000, 112.7521000],
        'probolinggo' => [-7.7569000, 113.2111000],
        'sumenep' => [-7.0000000, 113.8667000],
        'bangkalan' => [-7.0333000, 112.7500000],
        'palangka raya' => [-2.2088000, 113.9213000],
        'banjarbaru' => [-3.4533000, 114.8350000],
        'balikpapan' => [-1.2379000, 116.8529000],
        'mamuju' => [-2.6767000, 118.8861000],
    ];

    public function run(): void
    {
        $updated = 0;

        foreach (Location::whereNull('latitude')->cursor() as $location) {
            $key = self::normalize($location->city_regency);

            if ($key === null || ! isset(self::COORDINATES[$key])) {
                continue;
            }

            [$lat, $lng] = self::COORDINATES[$key];

            $location->update(['latitude' => $lat, 'longitude' => $lng]);
            $updated++;
        }

        $this->command?->info("Koordinat diisi untuk {$updated} lokasi.");
    }

    /**
     * Normalisasi nama kota/kabupaten menjadi kunci pencocokan.
     */
    public static function normalize(?string $name): ?string
    {
        $normalized = mb_strtolower(trim((string) $name));

        if ($normalized === '') {
            return null;
        }

        $normalized = preg_replace('/^kab\.?\.*\s+/', '', $normalized);
        $normalized = preg_replace('/^kota\s+/', '', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        $normalized = Str::squish($normalized);

        return $normalized;
    }
}
