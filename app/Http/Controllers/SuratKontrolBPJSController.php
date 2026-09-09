<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BpjsVclaimService;

class SuratKontrolBPJSController extends Controller
{
    //

     public function check(Request $request, BpjsVclaimService $bpjsVclaim)
    {
        $validated = $request->validate([
            'nosuratkontrol' => ['nullable', 'string', 'max:60'],
            'nokartu' => ['nullable', 'string', 'max:30'],
            'tanggalreservasi' => ['nullable', 'date_format:Y-m-d'],
            'kodepoli' => ['nullable', 'string', 'max:20'],
        ]);

        $noSuratKontrol = trim((string) ($validated['nosuratkontrol'] ?? ''));
        $noKartu = preg_replace('/\D+/', '', (string) ($validated['nokartu'] ?? ''));
        $tanggalReservasi = trim((string) ($validated['tanggalreservasi'] ?? ''));
        $kodePoli = strtoupper(trim((string) ($validated['kodepoli'] ?? '')));

        $canCheckByLetter = $noSuratKontrol !== '';
        $canCheckByCard = $noKartu !== ''
            && $tanggalReservasi !== ''
            && $kodePoli !== '';

        if (!$canCheckByLetter && !$canCheckByCard) {
            return response()->json([
                'success' => false,
                'found' => false,
                'match' => false,
                'message' => 'Untuk mengecek Surat Kontrol diperlukan nomor surat kontrol, atau No. Kartu BPJS + tanggal reservasi + kode poli BPJS.',
                'data' => null,
            ], 422);
        }

        try {
            /*
             * Bila konteks reservasi lengkap tersedia, gunakan method utama
             * agar hasil nomor surat tetap divalidasi silang.
             */
            if ($canCheckByLetter && $canCheckByCard) {
                $result = $bpjsVclaim->cekSuratKontrolReservasi(
                    $noKartu,
                    $tanggalReservasi,
                    $kodePoli,
                    $noSuratKontrol
                );
            } elseif ($canCheckByLetter) {
                $result = $bpjsVclaim->cekSuratKontrolByNomor(
                    $noSuratKontrol
                );
            } else {
                $result = $bpjsVclaim->cekSuratKontrolByNoKartu(
                    $noKartu,
                    $tanggalReservasi,
                    $kodePoli
                );
            }

            $data = is_array($result['data'] ?? null)
                ? $result['data']
                : [];

            $source = (string) ($result['source'] ?? '');

            if ($source === '') {
                $source = $canCheckByLetter
                    ? 'no_surat_kontrol'
                    : 'no_kartu';
            }

            $normalized = empty($data)
                ? null
                : $this->normalizeControlLetter($data);

            return response()->json([
                'success' => (bool) ($result['success'] ?? false),
                'found' => (bool) ($result['found'] ?? false),
                'match' => (bool) ($result['match'] ?? false),
                'message' => (string) ($result['message'] ?? ''),
                'source' => $source,
                'metode_label' => $source === 'no_kartu'
                    ? 'No. Kartu + Tanggal Reservasi + Kode Poli'
                    : 'Nomor Surat Kontrol',
                'warning' => $result['warning'] ?? null,
                'checks' => $result['checks'] ?? null,
                'data' => $normalized,
            ]);
        } catch (\Throwable $e) {
            Log::error('Cek Surat Kontrol BPJS gagal', [
                'nosuratkontrol' => $noSuratKontrol,
                'nokartu' => $noKartu,
                'tanggalreservasi' => $tanggalReservasi,
                'kodepoli' => $kodePoli,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'found' => false,
                'match' => false,
                'message' => 'Terjadi kesalahan saat memeriksa Surat Kontrol BPJS.',
                'data' => null,
            ], 500);
        }
    }

    private function normalizeControlLetter(array $data)
    {
        $jnsKontrol = (string) $this->firstValue($data, [
            'jnsKontrol',
            'jenisKontrol',
        ], '');

        return [
            'noSuratKontrol' => $this->firstValue($data, [
                'noSuratKontrol',
                'nosuratkontrol',
                'noSurat',
            ]),

            'noKartu' => $this->firstValue($data, [
                'noKartu',
                'nokartu',
                'no_kartu',
                'peserta.noKartu',
            ]),

            'tglRencanaKontrol' => $this->firstValue($data, [
                'tglRencanaKontrol',
                'tglKontrol',
                'tanggalRencanaKontrol',
            ]),

            'kodePoli' => $this->firstValue($data, [
                'poliTujuan',
                'kodePoli',
                'kodePoliTujuan',
                'poli.kode',
                'poliTujuan.kode',
            ]),

            'namaPoli' => $this->firstValue($data, [
                'namaPoliTujuan',
                'namaPoli',
                'poli.nama',
                'poliTujuan.nama',
            ]),

            'kodeDokter' => $this->firstValue($data, [
                'kodeDokter',
                'kodeDokterKontrol',
                'dokter.kode',
            ]),

            'namaDokter' => $this->firstValue($data, [
                'namaDokter',
                'namaDokterKontrol',
                'dokter.nama',
            ]),

            'jnsKontrol' => $jnsKontrol,
            'jenisKontrolLabel' => $this->jenisKontrolLabel($jnsKontrol),

            'tglTerbitKontrol' => $this->firstValue($data, [
                'tglTerbitKontrol',
                'tglTerbit',
                'tanggalTerbit',
            ]),

            'noSepAsalKontrol' => $this->firstValue($data, [
                'noSepAsalKontrol',
                'noSEPAsalKontrol',
                'noSep',
                'noSEP',
            ]),
        ];
    }

    private function firstValue(array $data, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            $value = data_get($data, $key);

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $default;
    }

    private function jenisKontrolLabel($value)
    {
        switch ((string) $value) {
            case '1':
                return 'SPRI';
            case '2':
                return 'Surat Kontrol';
            default:
                return $value !== '' ? $value : '-';
        }
    }
}
