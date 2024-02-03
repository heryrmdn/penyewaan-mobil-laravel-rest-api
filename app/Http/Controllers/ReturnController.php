<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_plat' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $rental = Rental::select('rentals.*', 'cars.merek', 'cars.model', 'cars.nomor_plat')
            ->join('cars', 'rentals.car_id', '=', 'cars.id')
            ->where('cars.nomor_plat', $request->nomor_plat)
            ->where('rentals.user_id', $request->user()->id)
            ->where('is_active', true)
            ->first();

        if (!$rental) {
            return response()->json(['message' => 'Data tidak ditemukan']);
        }

        Rental::where('id', $rental->id)->update(['is_active' => false]);
        return response()->json(['message' => 'Mobil berhasil dikembalikan']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
