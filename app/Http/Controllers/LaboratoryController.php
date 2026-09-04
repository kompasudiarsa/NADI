<?php

namespace App\Http\Controllers;

use App\Services\LaboratoryApiService;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    private $laboratory;

    public function __construct(
        LaboratoryApiService $laboratory
    ) {
        $this->laboratory = $laboratory;
    }

    /*
    |--------------------------------------------------------------------------
    | Form pencarian
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return view(
            'public.laboratory',
            [
                'result' => null,
                'noOrder' => null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cek hasil laboratorium
    |--------------------------------------------------------------------------
    */

     public function detailhasil(string $noOrder, string $lab)
    {
        $noOrder = strtoupper(trim($noOrder));
        $lab = strtoupper(trim($lab));
        $result = $this->laboratory->getHasil($noOrder);

        return view('layanan.laboratorium_detail', [
            'result' => $result,
            'noOrder' => $noOrder,
            'lab' => $lab,
        ]);
    }
}