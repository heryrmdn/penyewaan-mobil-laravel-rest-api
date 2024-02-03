<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $car = Car::all();

        if ($request->has('merek')) {
            $car = Car::where('merek', 'LIKE', '%' . $request->get('merek') . '%')->get();
        }
        if ($request->has('model')) {
            $car = Car::where('model', 'LIKE', '%' . $request->get('model') . '%')->get();
        }

        if ($request->has('ketersediaan') && $request->get('ketersediaan') == 'true') {
            $rental = Rental::select('car_id')
                ->where('tanggal_mulai', '<=', date('Y-m-d'))
                ->where('tanggal_selesai', '>=', date('Y-m-d'))
                ->get();

            $car = Car::whereNotIn('id', $rental)->get();
        }

        return response()->json(['data' => $car], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merek' => 'required',
            'model' => 'required',
            'nomor_plat' => 'required',
            'tarif_sewa_per_hari' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        if (Car::where('nomor_plat', $request->nomor_plat)->first()) {
            return response()->json(['message' => 'Mobil gagal ditambahkan'], 400);
        }

        // assign variable
        $request['user_id'] = $request->user()->id;

        Car::create(request()->all());

        return response()->json(['message' => 'Berhasil menambahkan mobil'], 200);
    }
}
