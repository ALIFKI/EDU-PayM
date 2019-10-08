<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\keranjang;
use Auth;
use App\listBarang;

class BeliController extends Controller
{

    public function listBarang(){
        $keranjang = Keranjang::where('transaksi_id', NULL)
                                ->where('pembeli_id', Auth::user()->id)
                                ->get();
        $list = ListBarang::all();

        return view('beli.listbarang', [
            'keranjangs' => $keranjang,
            'lists' => $list,
        ]);
    }

    public function MasukanBarang(Request $req)
    {
        $this->validate($req, [
            'nama_barang' => 'required',
            'harga_barang' => 'required',
            'jumlah_barang' => 'required'
        ]);

        $check = Keranjang::where('nama_barang', $req->nama_barang)
                            ->where('transaksi_id', NULL)
                            ->get();
        if(count($check)>0)
        {
            $keranjang = Keranjang::where('nama_barang', $req->nama_barang)
                                ->first();

            $keranjang->jumlah_barang += $req->jumlah_barang;
            $keranjang->harga_barang += $req->jumlah_barang * $req->harga_barang;            
            $keranjang->update();
        }
        else{
            $keranjang = new Keranjang;
            $keranjang->nama_barang = $req->nama_barang;
            $keranjang->jumlah_barang = $req->jumlah_barang;
            $keranjang->harga_barang = $req->harga_barang * $req->jumlah_barang;            
            $keranjang->pembeli_id = Auth::user()->id;
            $keranjang->save();
        }        
        return redirect(url('jualan'));
    }
    public function CancelBeli(){

        Keranjang::where('pembeli_id', Auth::user()->id)
                ->where('transaksi_id', NULL)
                ->delete();

        return redirect(url('index'));  
    }

    public function DeleteListBarang($id)
    {
        Keranjang::find($id)->delete();
        return redirect(url('jualan'));
    }

    public function Prosess()
    { 
        $length = 5;
        $randstring = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);

        $keranjangs = Keranjang::where('pembeli_id', Auth()->user()->id)
                                ->where('transaksi_id', NULL)
                                ->get();

        $total_harga = 0;
        for ($i=0;$i<count($keranjangs);$i++)
        {
            $total_harga += $keranjangs[$i]->harga_barang;            
        }
        

        // CLEAR TRANSAKSI CACHE
        Transaksi::where('pembeli_id', NULL)
                ->delete();

        $transaksi = new Transaksi;
        $transaksi->pembeli_id = Auth::user()->id;
        $transaksi->id_transaksi =  $randstring;     
        $transaksi->total_harga = $total_harga;
        $transaksi->save();

        $transaksi2 = Transaksi::where('pembeli_id', Auth::user()->id)
                                ->orderBy('id', 'DESC')->first();

        for ($i=0;$i<count($keranjangs);$i++)
        {            
            $keranjangs[$i]->transaksi_id = $transaksi2->id;
            $keranjangs[$i]->save();
        }

        $data = [
            'barang' => $keranjangs,
            'transaksi' => $transaksi2
        ];

        $file = 'img/qr.png';        
        \QRCode::text("$transaksi2->id")->setOutfile($file)->png();

        return view('pages.qrcode', [
            'qrfile' => $file
        ]);
    }
}
