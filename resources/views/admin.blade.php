@extends('layout')

@section('title', 'Admin Manages')

@section('content')
    <div class="container px-5 py-5">
        <h1 class="fs-3 text-center text-uppercase">Admin Access | Bounch Test BE - Parking</h1>
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
            <h2 class="fs-4 fw-bold text-uppercase">Filter Berdasarkan Tanggal</h2>
            <form action="{{ url('/admin') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-1">
                        <p class="fs-5 text-center text-uppercase fw-bold">Dari : </p>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <input type="date" class="form-control" id="start_date" name="start_date" required autocomplete="off" > 
                        </div>
                    </div>
                    <div class="col-md-1">
                        <p class="fs-5 text-center text-uppercase fw-bold">Ke :</p>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <input type="date" class="form-control" id="end_date" name="end_date" required autocomplete="off" > 
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group mb-3">
                            <select class="form-control" aria-label="Default select example" name="status" id="status">
                                <option value="1" selected>Pilih Status</option>
                                <option value="1">Semua</option>
                                <option value="2">Paid</option>
                                <option value="3">Unpaid</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <button class="btn btn-primary w-100" id="view_data" type="submit">Lihat</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <button class="btn btn-warning w-100" id="export_data" type="button">Export</button>
                                </div>
                            </div>
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
        // Function untuk menyimpan value terbaru dari input ke localStorage
        function saveLastInputValue(inputId) {
            const inputElement = document.getElementById(inputId);
            const inputValue = inputElement.value;
            localStorage.setItem(inputId, inputValue);
        }

        // Function untuk mengambil value terakhir dari localStorage dan mengatur value pada input
        function setLastInputValue(inputId) {
            const inputElement = document.getElementById(inputId);
            const lastValue = localStorage.getItem(inputId);

            if (lastValue) {
                inputElement.value = lastValue;
            }
        }

        // Panggil function setLastInputValue ketika halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', function() {
            setLastInputValue('start_date');
            setLastInputValue('end_date');
        });

        // Panggil function saveLastInputValue ketika nilai input berubah
        document.getElementById('start_date').addEventListener('change', function() {
            saveLastInputValue('start_date');
        });

        document.getElementById('end_date').addEventListener('change', function() {
            saveLastInputValue('end_date');
        });
    </script>

    <script>
        const exportBtn = document.getElementById('export_data');

        exportBtn.addEventListener('click', function() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const status = document.getElementById('status').value;

            const params = new URLSearchParams();
            params.append('start_date', startDate);
            params.append('end_date', endDate);
            params.append('status', status);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/export', true);
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            xhr.responseType = 'blob'; // Set the response type to blob (binary data)

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        const blob = new Blob([xhr.response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                        const url = URL.createObjectURL(blob);

                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'data_parkir.xlsx'; 
                        a.click();

                        URL.revokeObjectURL(url);

                        console.log('startDate:', startDate);
                        console.log('endDate:', endDate);
                        console.log('status:', status);

                    } else {
                        console.error('Export failed. Status:', xhr.status);
                    }
                }
            };

            xhr.send(params);
        });
    </script>
@endsection