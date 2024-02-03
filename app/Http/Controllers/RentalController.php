<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Rental;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RentalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rentals = Rental::select('rentals.*', 'cars.merek', 'cars.model', 'cars.nomor_plat')
            ->join('cars', 'cars.id', '=', 'rentals.car_id')
            ->where('rentals.user_id', $request->user()->id)
            ->get();

        return response()->json($rentals);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'car_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        if (!Car::where('id', $request->car_id)->first()) {
            return response()->json(['message' => 'Data mobil tidak ditemukan'], 400);
        }

        $rentals = Rental::where([
            ['car_id', $request->car_id],
            ['tanggal_mulai', '<=', $request->tanggal_mulai],
            ['tanggal_selesai', '>=', $request->tanggal_mulai]
        ])
            ->orWhere([
                ['car_id', $request->car_id],
                ['tanggal_mulai', '>', $request->tanggal_mulai],
                ['tanggal_mulai', '<', $request->tanggal_selesai]
            ])
            ->orWhere([
                ['car_id', $request->car_id],
                ['tanggal_mulai', $request->tanggal_selesai]
            ])
            ->get();

        if (sizeof($rentals) > 0) {
            return response()->json(['message' => 'Mobil tidak dapat disewa'], 400);
        }

        // computed & assign variable
        $request['user_id'] = $request->user()->id;

        $diff = date_diff(date_create_immutable($request->tanggal_mulai), date_create_immutable($request->tanggal_selesai))->d + 1;
        $request['jumlah_hari_penyewaan'] = $diff;

        $car = Car::where('id', $request->car_id)->first();
        $request['jumlah_biaya_sewa'] = $car->tarif_sewa_per_hari * $request->jumlah_hari_penyewaan;
        $request['is_active'] = true;

        Rental::create(request()->all());

        return response()->json(['message' => 'Berhasil menambahkan jadwal'], 200);
    }
}
