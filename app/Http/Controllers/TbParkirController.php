<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB; 
use App\Models\TbParkir;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Illuminate\Http\Request;

class TbParkirController extends Controller
{
    public function view_first()
    {
        return view('welcome');
    }
    
    public function view_user()
    {
        $datasparkir = TbParkir::orderBy('updated_at', 'DESC')->get();

        foreach ($datasparkir as $parkir) {
            if ($parkir->clock_in && $parkir->clock_out) {
                $totalDetik = $parkir->clock_out - $parkir->clock_in;

                $jam = floor($totalDetik / 3600);
                $sisaDetik = $totalDetik % 3600;
                $menit = floor($sisaDetik / 60);

                $parkir->formatted_duration = $jam . ' Jam ' . $menit . ' Menit';
            } else {
                $parkir->formatted_duration = '';
            }
        }

        return view('user', compact('datasparkir'));
    }

    public function view_admin(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $query = TbParkir::orderBy('updated_at', 'DESC');

        if ($status != 1) {
            if ($status == 2) {
                $query->where('status', '!=', 0); 
            } elseif ($status == 3) {
                $query->where('status', 0); 
            }
        }

        if ($startDate && $endDate) {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $datasparkir = $query->get();

        foreach ($datasparkir as $parkir) {
            if ($parkir->clock_in && $parkir->clock_out) {
                $totalDetik = $parkir->clock_out - $parkir->clock_in;

                $jam = floor($totalDetik / 3600);
                $sisaDetik = $totalDetik % 3600;
                $menit = floor($sisaDetik / 60);

                $parkir->formatted_duration = $jam . ' Jam ' . $menit . ' Menit';
            } else {
                $parkir->formatted_duration = '';
            }
        }

        return view('admin', compact('datasparkir'));
    }
    
    public function store(Request $request)
    {
        // Validasi request yang diterima dari form
        $validatedData = $request->validate([
            'kode_area' => 'required',
            'nomor_seri' => 'required',
            'kode_kota' => 'required',
        ]);

        $nopol = strtoupper($request->input('kode_area')) . ' ' .
                 strtoupper($request->input('nomor_seri')) . ' ' .
                 strtoupper($request->input('kode_kota'));

        $unikCode = Str::upper(Str::random(10));
        $clock_in = time();

        $data = array(
            'unicode' => $unikCode,
            'nopol' => $nopol,
            'clock_in' => $clock_in,
            'status' => 0
        );

        TbParkir::create($data);

        // Redirect atau berikan respon sukses
        return redirect()->back()->with('success', 'Data berhasil disimpan.');
    }

    public function pay(Request $request)
    {
        $validatedData = $request->validate([
            'unix_code' => 'required',
        ]);

        $unix_code = $request->input('unix_code');

        $parkirData = TbParkir::where('unicode', $unix_code)->first();
        if (!$parkirData) {
            return redirect()->back()->with('error', 'Kode Parkir Tidak Ditemukan');
        }

        if ($parkirData->status != 0) {
            return redirect()->back()->with('error', 'Kendaraan Sudah meninggalkan Parkiran');
        }

        // cek durasi
        $clock_out = time();
        $durasiDetik = $clock_out - $parkirData->clock_in;

        // Konversi durasi parkir ke dalam format jam dan menit
        $durasiJam = floor($durasiDetik / 3600);
        $sisaDetik = $durasiDetik % 3600;
        $durasiMenit = floor($sisaDetik / 60);

        // Jika ada sisa detik lebih dari 1 menit, tambahkan 1 jam ke durasi jam
        if ($sisaDetik > 60) {
            $durasiJam++;
        }

        $hargaParkir = 3000 * $durasiJam;
        if ($durasiJam == 0) {
            $hargaParkir = 3000;
        }

        $parkirData->update([
            'clock_out' => $clock_out,
            'price' => $hargaParkir,
            'status' => 1,
        ]);

        return redirect()->back()->with('success', 'Pembayaran Berhasil. Harga Parkir : Rp. ' . number_format($hargaParkir, 2, ',', '.'));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // Query data berdasarkan filter yang diberikan
        $query = TbParkir::orderBy('updated_at', 'DESC');
        if ($status != 1) {
            if ($status == 2) {
                $query->where('status', '!=', 0);
            } elseif ($status == 3) {
                $query->where('status', 0);
            }
        }
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDateTime, $endDateTime]);
        }
        $datasparkir = $query->get();

        // Buat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul kolom
        $sheet->setCellValue('A1', 'Kode Parkir');
        $sheet->setCellValue('B1', 'Nomor Polisi');
        $sheet->setCellValue('C1', 'Jam Masuk');
        $sheet->setCellValue('D1', 'Jam Keluar');
        $sheet->setCellValue('E1', 'Durasi');
        $sheet->setCellValue('F1', 'Harga');
        $sheet->setCellValue('G1', 'Status');

        // Isi data dari hasil query
        $row = 2;
        foreach ($datasparkir as $parkir) {
            // Hitung durasi parkir
            if ($parkir->clock_in && $parkir->clock_out) {
                $totalDetik = $parkir->clock_out - $parkir->clock_in;

                $jam = floor($totalDetik / 3600);
                $sisaDetik = $totalDetik % 3600;
                $menit = floor($sisaDetik / 60);

                $parkir->formatted_duration = $jam . ' Jam ' . $menit . ' Menit';
            } else {
                $parkir->formatted_duration = '';
            }

            $sheet->setCellValue('A' . $row, $parkir->unicode);
            $sheet->setCellValue('B' . $row, $parkir->nopol);
            $sheet->setCellValue('C' . $row, date('Y/m/d H:i', $parkir->clock_in));
            $sheet->setCellValue('D' . $row, ($parkir->clock_out == NULL) ? '' : date('Y/m/d H:i', $parkir->clock_out));
            $sheet->setCellValue('E' . $row, $parkir->formatted_duration);
            $sheet->setCellValue('F' . $row, ($parkir->price == NULL) ? '' : 'Rp. ' . number_format($parkir->price, 2, ',', '.'));
            $sheet->setCellValue('G' . $row, ($parkir->status == 0) ? "Unpaid" : "Paid");

            $row++;
        }

        // Buat response untuk file Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'data_parkir_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
}
