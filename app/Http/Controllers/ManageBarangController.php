<?php

namespace App\Http\Controllers;
use App\listBarang;
use Illuminate\Http\Request;

class ManageBarangController extends Controller
{
    public function add(){

        return view('management.add');
    }
    public function create(Request $req){

        

    }
}
