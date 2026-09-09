@extends('layouts.app', [
    'title' => 'Cek Surat Kontrol BPJS'
])

@section('content')

<div class="container py-4">

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h4 class="mb-3">
                Surat Kontrol BPJS
            </h4>

            @if(data_get($result, 'found'))

                @if(data_get($result, 'match'))

                    <div class="alert alert-success">
                        Surat kontrol ditemukan
                        dan sesuai dengan reservasi.
                    </div>

                @else

                    <div class="alert alert-warning">
                        Surat kontrol ditemukan,
                        tetapi terdapat data yang
                        tidak sesuai dengan reservasi.
                    </div>

                @endif

                <table class="table table-sm">

                    <tr>
                        <th width="35%">
                            Nomor Surat
                        </th>
                        <td>
                            {{
                                data_get(
                                    $result,
                                    'data.noSuratKontrol',
                                    '-'
                                )
                            }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            Nomor Kartu
                        </th>
                        <td>
                            {{
                                data_get(
                                    $result,
                                    'data.noKartu',
                                    '-'
                                )
                            }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            Tanggal Kontrol
                        </th>
                        <td>
                            {{
                                data_get(
                                    $result,
                                    'data.tglRencanaKontrol',
                                    '-'
                                )
                            }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            Poli Tujuan
                        </th>
                        <td>
                            {{
                                data_get(
                                    $result,
                                    'data.namaPoliTujuan',
                                    data_get(
                                        $result,
                                        'data.poliTujuan',
                                        '-'
                                    )
                                )
                            }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            Dokter
                        </th>
                        <td>
                            {{
                                data_get(
                                    $result,
                                    'data.namaDokter',
                                    '-'
                                )
                            }}
                        </td>
                    </tr>

                </table>

            @else

                <div class="alert alert-danger">
                    {{
                        data_get(
                            $result,
                            'message',
                            'Surat kontrol tidak ditemukan.'
                        )
                    }}
                </div>

            @endif

            <a
                href="{{ url()->previous() }}"
                class="btn btn-outline-primary"
            >
                Kembali
            </a>

        </div>
    </div>

</div>

@endsection