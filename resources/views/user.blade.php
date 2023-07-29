@extends('layout')

@section('title', 'User Manages')

@section('content')
    <div class="container px-5 py-5">
        <h1 class="fs-3 text-center text-uppercase">User Access | Bounch Test BE - Parking</h1>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-warning">
                {{ session('error') }}
            </div>
        @endif
        <div class="container my-5 me-5">
            <h2 class="fs-4 fw-bold text-uppercase">Input kendaraan masuk</h2>
            <form action="{{ url('/add') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-2">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Kode Area" id="kode_area" name="kode_area" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Nomor seri" id="nomor_seri" name="nomor_seri" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Kode Kota" id="kode_kota" name="kode_kota" required autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit">Masuk</button>
                </div>
            </form>
        </div>
        
        <div class="container my-5 me-5">
            <h2 class="fs-4 fw-bold text-uppercase">Input kendaraan Keluar</h2>
            <form action="{{ url('/pay') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Masukan Kode Parkir" aria-label="Masukan Kode Parkir" aria-describedby="basic-addon2" id="unix_code" name="unix_code" required autocomplete="off">
                            <button type="submit" class="btn btn-primary" id="basic-addon2">Proses</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="container my-5">
            <table class="table table-striped">
                <thead>
                  <tr>
                    <th class="text-center" scope="col">#</th>
                    <th class="text-center" scope="col">Kode Parkir</th>
                    <th class="text-center" scope="col">Nomor Polisi</th>
                    <th class="text-center" scope="col">Jam Masuk</th>
                    <th class="text-center" scope="col">Jam Keluar</th>
                    <th class="text-center" scope="col">Durasi</th>
                    <th class="text-center" scope="col">Harga</th>
                    <th class="text-center" scope="col">Status</th>
                  </tr>
                  
                </thead>
                <tbody>
                    @foreach($datasparkir as $parkir)
                        <tr>
                            <th class="text-center" scope="row">{{ $loop->iteration }}</th>
                            <td class="text-center">{{ $parkir->unicode }}</td>
                            <td class="text-center">{{ $parkir->nopol }}</td>
                            <td class="text-center">{{ date('Y/m/d H:i', $parkir->clock_in) }}</td>
                            <td class="text-center">{{ ($parkir->clock_out == NULL) ? '' : date('Y/m/d H:i', $parkir->clock_out) }}</td>
                            <td class="text-center">{{ $parkir->formatted_duration }}</td>
                            <td class="text-center">{{ ($parkir->price == NULL) ? '' : 'Rp. ' . number_format($parkir->price, 2, ',', '.') }}</td>
                            <td class="text-center"><?= ($parkir->status == 0) ? "<span class='btn btn-sm btn-warning'>Unpaid</span>" : "<span class='btn btn-sm btn-success'>Paid</span>"; ?></td>
                        </tr>
                    @endforeach
                </tbody>
              </table>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        const kodeAreaInput = document.getElementById('kode_area');
        const nomorSeriInput = document.getElementById('nomor_seri');
        const kodeKotaInput = document.getElementById('kode_kota');
        const unixCodeInput = document.getElementById('unix_code');
    
        kodeAreaInput.addEventListener('input', function() {
        kodeAreaInput.value = kodeAreaInput.value.toUpperCase().replace(/[^A-Z]/g, '').substring(0, 2);
        if (kodeAreaInput.value.length === 2) {
            nomorSeriInput.focus();
        }
        });
    
        nomorSeriInput.addEventListener('input', function() {
        nomorSeriInput.value = nomorSeriInput.value.replace(/\D/g, '').substring(0, 4);
        if (nomorSeriInput.value.length === 4) {
            kodeKotaInput.focus();
        }
        });
    
        kodeKotaInput.addEventListener('input', function() {
        kodeKotaInput.value = kodeKotaInput.value.toUpperCase().replace(/[^A-Z]/g, '').substring(0, 3);
        });

        unixCodeInput.addEventListener('input', function() {
        unixCodeInput.value = unixCodeInput.value.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 10);
        });
    </script>
@endsection