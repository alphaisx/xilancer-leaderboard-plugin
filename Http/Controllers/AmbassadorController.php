<?php

namespace Modules\Rank\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;


class AmbassadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function admin_index()
    {
        // Logic for ambassador leaderboard view
        return view('rank::admin.ambassador');
    }
}
