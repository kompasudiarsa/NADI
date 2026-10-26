<?php

namespace App\Http\Controllers;

use App\Models\NadiReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NadiReviewController extends Controller
{
    private function patientKey(Request $request): ?string
    {
        $patient = $request->session()->get('pasien', []);
        if (!is_array($patient)) return null;

        // Prioritaskan ID pasien yang stabil agar ulasan tetap milik pasien yang sama.
        $id = $patient['patient_id'] ?? null;
        if ($id === null || trim((string) $id) === '') {
            $id = $patient['medical_record'] ?? $patient['rm'] ?? $patient['nocm'] ?? $patient['no_rm'] ?? null;
        }
        if ($id === null || trim((string) $id) === '') return null;

        // Hindari menyimpan nomor RM asli di tabel publik ulasan.
        return hash_hmac('sha256', trim((string) $id), config('app.key'));
    }

    private const FOCUS_CATEGORIES = [
        'waktu_tunggu_antrean' => 'Waktu tunggu dan antrean',
        'sikap_perawat_staf' => 'Sikap perawat dan staf',
        'pendaftaran_administrasi' => 'Pendaftaran dan administrasi',
        'dokter_penjelasan_medis' => 'Dokter dan penjelasan medis',
        'biaya_penjaminan' => 'Biaya dan penjaminan',
        'farmasi_obat' => 'Farmasi dan obat',
        'kebersihan_fasilitas' => 'Kebersihan dan fasilitas',
        'parkir_akses' => 'Parkir dan akses',
        'lainnya' => 'Lainnya',
    ];

    public function index(Request $request)
    {
        $patientKey = $this->patientKey($request);
        $query = NadiReview::query()->where('status', 'published');

        $stats = (clone $query)->selectRaw('COUNT(*) as total, AVG(rating) as average_rating')->first();
        $distribution = (clone $query)->select('rating')
            ->selectRaw('COUNT(*) as total')->groupBy('rating')->pluck('total', 'rating')->all();

        $service = $request->query('service', 'semua');
        $allowed = ['semua', 'rumah_sakit', 'poli', 'layanan'];
        if (!in_array($service, $allowed, true)) $service = 'semua';

        $reviews = (clone $query)
            ->when($service !== 'semua', fn ($q) => $q->where('service_type', $service))
            ->orderByDesc('published_at')->orderByDesc('id')->paginate(10)->withQueryString();

        $myReviews = $patientKey
            ? NadiReview::where('patient_key', $patientKey)->latest('id')->get()
            : collect();

        $focusCategories = self::FOCUS_CATEGORIES;
        return view('nadi-reviews.reviews', compact('reviews', 'myReviews', 'stats', 'distribution', 'patientKey', 'service', 'focusCategories'));
    }

    public function store(Request $request)
    {
        $patientKey = $this->patientKey($request);
        if (!$patientKey) return redirect('/')->with('error', 'Silakan masuk ke NADI terlebih dahulu.');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'service_type' => ['required', Rule::in(['rumah_sakit', 'poli', 'layanan'])],
            'service_name' => ['nullable', 'string', 'max:120', 'required_unless:service_type,rumah_sakit'],
            'focus_category' => ['required', Rule::in(array_keys(self::FOCUS_CATEGORIES))],
            'review' => ['required', 'string', 'min:8', 'max:2000'],
        ]);

        $serviceName = $data['service_type'] === 'rumah_sakit'
            ? null : trim((string) ($data['service_name'] ?? ''));
        if ($data['service_type'] !== 'rumah_sakit' && $serviceName === '') {
            return back()->withErrors(['service_name' => 'Nama unit/layanan wajib diisi.'])->withInput();
        }

        NadiReview::create([
            'patient_key' => $patientKey,
            'patient_label' => 'Pasien terverifikasi',
            'rating' => $data['rating'],
            'service_type' => $data['service_type'],
            'service_name' => $serviceName,
            'focus_category' => $data['focus_category'],
            'review' => trim($data['review']),
            'status' => 'published',
            'published_at' => now(),
        ]);

        return redirect()->route('nadi.reviews.index')->with('success', 'Ulasan berhasil diterbitkan dan langsung terlihat.');
    }

    public function destroy(Request $request, NadiReview $review)
    {
        $patientKey = $this->patientKey($request);
        abort_unless($patientKey && hash_equals((string) $review->patient_key, $patientKey), 403,
            'Anda hanya boleh menghapus ulasan milik sendiri.');

        $review->delete();
        return redirect()->route('nadi.reviews.index')->with('success', 'Ulasan Anda telah dihapus.');
    }
}
