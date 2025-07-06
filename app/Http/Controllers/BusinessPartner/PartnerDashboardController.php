<?php

namespace App\Http\Controllers\BusinessPartner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PartnerDashboardController extends Controller
{
    public function index()
    {
        return view('business-partner.dashboard.index');
    }
}
