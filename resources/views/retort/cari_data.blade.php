@php
    use Illuminate\Support\Str;

    $hiddenColumns = ['id', 'uuid', 'created_at', 'updated_at', 'deleted_at'];
@endphp

<div class="container-fluid">

    @php
        $tables = [
            'AREA HYGIENE' => $area_hygienes,
            'AREA SUHU' => $area_suhus,
            'SANITASI' => $sanitasis,
            'SUHU' => $suhus,
            'PRODUKSI' => $produksis,
            'PACKING' => $packings,
            'SAMPLING' => $samplings,
            'SAMPEL' => $sampels,
        ];
    @endphp

    @foreach ($tables as $title => $data)
        @continue($data->isEmpty())

        <div class="card shadow-sm border-0 mb-4">

            {{-- HEADER --}}
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">{{ $title }}</span>
                <span class="badge bg-light text-dark">{{ $data->count() }}</span>
            </div>

            {{-- TABLE --}}
            <div class="table-responsive">

                <table class="table table-hover table-striped table-sm align-middle mb-0">

                    {{-- HEADER --}}
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width:50px;">No</th>

                            @foreach (array_keys((array) $data->first()) as $col)
                                @continue(in_array($col, $hiddenColumns))
                                <th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>
                            @endforeach
                        </tr>
                    </thead>

                    {{-- BODY --}}
                    <tbody>
                        @foreach ($data as $i => $row)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>

                                @foreach ((array) $row as $key => $value)
                                    @continue(in_array($key, $hiddenColumns))

                                    <td style="max-width: 250px; word-wrap: break-word;">

                                        {{-- ============================= --}}
                                        {{-- 🔥 PLANT UUID → NAMA --}}
                                        {{-- ============================= --}}
                                        @if ($key == 'plant' && isset($row->plant_nama))
                                            <span class="badge bg-info text-dark">
                                                {{ $row->plant_nama }}
                                            </span>

                                            {{-- ============================= --}}
                                            {{-- 🔥 FILE --}}
                                            {{-- ============================= --}}
                                        @elseif (Str::contains($key, ['file', 'foto', 'gambar']) && $value)
                                            <a href="{{ asset('storage/' . $value) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                Lihat File
                                            </a>

                                            {{-- ============================= --}}
                                            {{-- 🔥 JSON (OPTIMIZED) --}}
                                            {{-- ============================= --}}
                                        @elseif (is_string($value) && Str::startsWith($value, ['{', '[']))
                                            @php
                                                $decoded = json_decode($value, true);
                                            @endphp

                                            @if (json_last_error() === JSON_ERROR_NONE && is_array($decoded))

                                                <div class="bg-light border rounded p-2 small"
                                                    style="max-height:180px; overflow:auto;">

                                                    @foreach ($decoded as $item => $detail)
                                                        {{-- CASE 1: FORMAT OBJECT --}}
                                                        @if (is_array($detail))
                                                            <div class="mb-2">
                                                                <strong>{{ $item }}</strong>

                                                                <ul class="mb-1 ps-3">
                                                                    @foreach ($detail as $k => $v)
                                                                        @continue(empty($v))

                                                                        <li>
                                                                            @if ($v == '✔')
                                                                                <span class="badge bg-success">OK</span>
                                                                            @elseif ($k == 'keterangan')
                                                                                <span
                                                                                    class="text-danger">{{ $v }}</span>
                                                                            @else
                                                                                {{ $k }}: {{ $v }}
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>

                                                            {{-- CASE 2: FORMAT ARRAY (hasil_suhu) --}}
                                                        @elseif (is_array($decoded) && isset($decoded[0]['area']))
                                                            @foreach ($decoded as $rowJson)
                                                                <div>
                                                                    {{ $rowJson['area'] ?? '-' }} :
                                                                    <strong>{{ $rowJson['nilai'] ?? '-' }}</strong>
                                                                </div>
                                                            @endforeach
                                                            @break
                                                        @endif
                                                    @endforeach

                                                </div>
                                            @else
                                                {{ $value }}
                                            @endif

                                            {{-- ============================= --}}
                                            {{-- 🔥 NORMAL --}}
                                            {{-- ============================= --}}
                                        @else
                                            {{ $value ?? '-' }}
                                        @endif

                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>
        </div>
    @endforeach

</div>
