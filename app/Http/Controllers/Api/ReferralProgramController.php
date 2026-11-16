<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReferralProgram;

class ReferralProgramController extends Controller
{
    public function index()
    {
        $programs = ReferralProgram::withCount('referrals')->latest()->paginate(15);

        return response()->json($programs);
    }
}