<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use DB;
use Carbon\Carbon;
use DateTime;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        
        if (view()->exists($request->path())) 
        {
            /*listar las clientes en ventana modal*/
        
            // $clientes=DB::table('clientes')->get();
        
            // /*listar los productos en ventana modal*/
            // $productos=DB::table('productos as p')
            // ->select(DB::raw('CONCAT(p.ArtCode," ",p.descripcion) AS producto'),'p.id')
            // ->get(); 

            // $nro_factura = DB::table('ventas as v')
            // ->select(DB::raw('MAX(v.fact_nro) as fact_nro'))
            // ->where('v.estado','=',0)
            // ->get();

            // $bancos=DB::table('bancos')
            // ->select('bancos.id','bancos.descripcion')
            // ->get();

            // $cuentas=DB::table('cuentas_corriente as cc')
            // ->join('bancos as b','b.id','=','cc.banco_id')
            // ->select('cc.id','cc.nro_cuenta','cc.banco_id','b.descripcion as banco')
            // ->get();
            // //dd($nro_factura);

            // // return Redirect::to('factura.create',["clientes"=>$clientes,"productos"=>$productos,
            // // "nro_factura"=>$nro_factura,"bancos"=>$bancos,"cuentas"=>$cuentas]);
            // return Redirect::to('factura/create')
            // ->with("clientes",$clientes)->with("productos",$productos)
            // ->with("nro_factura",$nro_factura)->with("bancos",$bancos)
            // ->with("cuentas",$cuentas);

            //PEDIR COTIZACIÓN ANTES DE EMPEZAR
            // $fecha_hoy = Carbon::now('America/Asuncion');
            // $fecha_hoy = $fecha_hoy->format('Y-m-d');

            // $dolar=DB::table('cotizaciones as c')
            // //->join('empresas','clientes.idempresa','=','empresas.id')
            // ->select('c.id','c.moneda','c.dolCompra','c.dolVenta',
            // 'psCompra','psVenta','rsCompra','rsVenta','c.fecha','c.estado')
            // ->where('fecha','=',$fecha_hoy)
            // ->first();
            //dd($cotizacion);
            // if($dolar==null)
            // {
            //     $cotizaciones=DB::table('cotizaciones as c')
            //     //->join('empresas','clientes.idempresa','=','empresas.id')
            //     ->select('c.id','c.moneda','c.dolCompra','c.dolVenta',
            //     'psCompra','psVenta','rsCompra','rsVenta','c.fecha','c.estado')
            //     ->orderBy('c.fecha','desc')
            //     ->get();
            //     //return view('cotizacion.index',["cotizaciones"=>$cotizaciones])->with('msj', 'FAVOR CARGAR LA COTIZACIÓN DEL DÍA');
            //     return Redirect::to("cotizacion")->with('msj', 'FAVOR CARGAR LA COTIZACIÓN DEL DÍA');
            // }
            //else
            //{
                return Redirect::to("factura/create");
            //}
        }
        return abort(404);
    }

    public function root()
    {
        /*listar las clientes en ventana modal*/
        // $clientes=DB::table('clientes')->get();
       
        // /*listar los productos en ventana modal*/
        // $productos=DB::table('productos as p')
        // ->select(DB::raw('CONCAT(p.ArtCode," ",p.descripcion) AS producto'),'p.id')
        // ->get(); 

        // $nro_factura = DB::table('ventas as v')
        // ->select(DB::raw('MAX(v.fact_nro) as fact_nro'))
        // ->where('v.estado','=',0)
        // ->get();

        // $bancos=DB::table('bancos')
        // ->select('bancos.id','bancos.descripcion')
        // ->get();

        // $cuentas=DB::table('cuentas_corriente as cc')
        // ->join('bancos as b','b.id','=','cc.banco_id')
        // ->select('cc.id','cc.nro_cuenta','cc.banco_id','b.descripcion as banco')
        // ->get();
        // //dd($nro_factura);

        // return Redirect::to('factura/create')
        // ->with("clientes",$clientes)->with("productos",$productos)
        // ->with("nro_factura",$nro_factura)->with("bancos",$bancos)
        // ->with("cuentas",$cuentas);

         //PEDIR COTIZACIÓN ANTES DE EMPEZAR
        //  $fecha_hoy = Carbon::now('America/Asuncion');
        //  $fecha_hoy = $fecha_hoy->format('Y-m-d');

        //  $dolar=DB::table('cotizaciones as c')
        //  //->join('empresas','clientes.idempresa','=','empresas.id')
        //  ->select('c.id','c.moneda','c.dolCompra','c.dolVenta',
        //  'psCompra','psVenta','rsCompra','rsVenta','c.fecha','c.estado')
        //  ->where('fecha','=',$fecha_hoy)
        //  ->first();
        //  //dd($cotizacion);
        //  if($dolar==null)
        //  {
        //      $cotizaciones=DB::table('cotizaciones as c')
        //      //->join('empresas','clientes.idempresa','=','empresas.id')
        //      ->select('c.id','c.moneda','c.dolCompra','c.dolVenta',
        //      'psCompra','psVenta','rsCompra','rsVenta','c.fecha','c.estado')
        //      ->orderBy('c.fecha','desc')
        //      ->get();
        //      //return view('cotizacion.index',["cotizaciones"=>$cotizaciones])->with('msj', 'FAVOR CARGAR LA COTIZACIÓN DEL DÍA');
        //      return Redirect::to("cotizacion")->with('msj', 'FAVOR CARGAR LA COTIZACIÓN DEL DÍA');
        //  }
         //else
            //{
                return Redirect::to("factura/create");
            //}
    }

    /*Language Translation*/
    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }

    public function updateProfile(Request $request, $id)
    {
        // return $request->all();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'dob' => ['required', 'date', 'before:today'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        $user = User::find($id);
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->dob = date('Y-m-d', strtotime($request->get('dob')));

        if ($request->file('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            $avatar->move($avatarPath, $avatarName);
            if (file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }
            $user->avatar = '/images/' . $avatarName;
        }
        $user->update();
        if ($user) {
            Session::flash('message', 'User Details Updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return response()->json([
                'isSuccess' => true,
                'Message' => "User Details Updated successfully!"
            ], 200); // Status code here
        } else {
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
            return response()->json([
                'isSuccess' => true,
                'Message' => "Something went wrong!"
            ], 200); // Status code here
        }
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json([
                'isSuccess' => false,
                'Message' => "Your Current password does not matches with the password you provided. Please try again."
            ], 200); // Status code 
        } else {
            $user = User::find($id);
            $user->password = Hash::make($request->get('password'));
            $user->update();
            if ($user) {
                Session::flash('message', 'Password updated successfully!');
                Session::flash('alert-class', 'alert-success');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Password updated successfully!"
                ], 200); // Status code here
            } else {
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Something went wrong!"
                ], 200); // Status code here
            }
        }
    }
}