<?php

namespace App\Http\Controllers\user_interface;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Navbar extends Controller
{
  public function index()
  {
    return view('content.user-interface.ui-navbar');
  }
}
