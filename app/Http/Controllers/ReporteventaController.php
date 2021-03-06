<?php

namespace SisVentas\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;

use SisVentas\Http\Requests;
use SisVentas\Articulo;
use SisVentas\Http\Requests\ArticuloFormRequest;
use DB;

use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection as Collection;


class ReporteventaController extends Controller
{
   /************************/
    /** REPORTES DE VENTAS **/
    /************************/
    public function reporte_venta_cliente(Request $request)
    {
        $clientes=DB::table('tb_persona')->orderBy('idpersona')->get();

        $f1 = Carbon::now()->toDateString("FechaInicio");
        $f2 = Carbon::now()->toDateString("FechaFinal");

        $f1=$request->get('FechaInicio');
        $f2=$request->get('FechaFinal');

        $clien = $request->get('cliente');
        $muni = $request->get('municipio');

            $ventas=DB::table('tb_venta as v')
                ->join('tb_persona as p','v.idcliente','=','p.idpersona')
                ->join('tb_persona as p2','v.idvendedor','=','p2.idpersona')
                ->join('tb_detalle_venta as dv','v.idventa','=','dv.idventa')
                ->select('v.idventa','v.idvendedor','v.fecha_hora','v.idcliente','p.nombre','p2.nombre as vendedor','p.municipio','v.tipo_comprobante','v.serie_comprobante','v.num_comprobante','v.estado','v.total_venta','v.total_noce')
                ->where(function($query) use ($clien,$muni,$f1,$f2){
                    if (($f1) & ($f2)) {
                        if (($f1 != "") & ($f2 != "") & ($clien != "")) {
                            return $query->WhereBetween('v.fecha_hora', [$f1,$f2])
                                            ->where(function($q) use ($clien,$muni){
                                                $q->orWhere('p.idpersona',$clien)
                                                ->orWhere('p.municipio',$muni);
                                            });
                        }else{
                            if (($f1 != "") & ($f2 != "")){
                                return $query->WhereBetween('v.fecha_hora', [$f1,$f2]);
                            }else{
                                return $query->whereMonth('v.fecha_hora', date('m'));
                            }
                        }
                    }
                    if ($clien) {
                        if ($clien != "") {
                             return $query->where('p.idpersona',$clien)
                                        /*->where(function($q){
                                            $q->whereMonth('v.fecha_hora', date('m'));
                                        })*/;
                        }
                    }
                    if ($muni) {
                        if ($muni != "") {
                             return $query->where('p.municipio',$muni);
                        }
                    }
                })
                ->groupBy('v.idventa','v.fecha_hora','p.nombre','v.idcliente','p2.nombre','p.municipio','v.tipo_comprobante','v.serie_comprobante','v.num_comprobante','v.estado','v.total_venta','v.total_noce')
                ->where([
                    ['v.estado','<>','Anulada'],
                    ['v.estado','<>','Eliminada']
                ])
                ->orderBy('idventa','desc')
                ->paginate(100);

            $sum_total_venta = 0;
            foreach ($ventas as $venta) {
                $sum_total_venta += $venta->total_venta - $venta->total_noce;
            }
        return view('reporte.venta.venta-cliente.index',["ventas"=>$ventas,"clientes"=>$clientes,"sum_total_venta"=>$sum_total_venta]);
    }

    public function reporte_venta_vendedor(Request $request)
    {
        $vendedores=DB::table('tb_persona')->where('tipo_persona','Vendedor')->get();
        $vende = trim($request->get('searchVendedor'));

        $f1 = Carbon::now()->toDateString("FechaInicio");
        $f2 = Carbon::now()->toDateString("FechaFinal");

        $f1=$request->get('FechaInicio');
        $f2=$request->get('FechaFinal');

        $muni = $request->get('municipio');

            $ventas=DB::table('tb_venta as v')
                ->join('tb_persona as p','v.idcliente','=','p.idpersona')
                ->join('tb_persona as p2','v.idvendedor','=','p2.idpersona')
                ->join('tb_detalle_venta as dv','v.idventa','=','dv.idventa')
                ->select('v.idventa','v.idvendedor','v.fecha_hora','v.idcliente','p.nombre','p2.nombre as vendedor','p.municipio','v.tipo_comprobante','v.serie_comprobante','v.num_comprobante','v.estado','v.total_venta','v.total_noce')
                ->where(function($query) use ($vende,$muni,$f1,$f2){
                    if (($f1) & ($f2)) {
                        if (($f1 != "") & ($f2 != "") & ($vende != "")) {
                            return $query->WhereBetween('v.fecha_hora', [$f1,$f2])
                                            ->where(function($q) use ($vende,$muni){
                                                $q->Where('v.idvendedor',$vende);
                                            });
                        }else{
                            if (($f1 != "") & ($f2 != "")){
                                return $query->WhereBetween('v.fecha_hora', [$f1,$f2]);
                            }else{
                                return $query->whereMonth('v.fecha_hora', date('m'));
                            }
                        }
                    }
                    if ($vende) {
                        if ($vende != "") {
                             return $query->where('v.idvendedor',$vende)
                                        ->where(function($q){
                                            $q->whereMonth('v.fecha_hora', date('m'));
                                        });
                        }
                    }
                })
                ->groupBy('v.idventa','v.fecha_hora','p.nombre','v.idcliente','p2.nombre','p.municipio','v.tipo_comprobante','v.serie_comprobante','v.num_comprobante','v.estado','v.total_venta','v.total_noce')
                ->where([
                    ['v.estado','<>','Anulada'],
                    ['v.estado','<>','Eliminada']
                ])
                ->orderBy('idventa','desc')
                ->paginate(100);

            $sum_total_venta = 0;
            foreach ($ventas as $venta) {
                $sum_total_venta += $venta->total_venta - $venta->total_noce;
            }
        return view('reporte.venta.venta-vendedor.index',["ventas"=>$ventas,"vendedores"=>$vendedores,"sum_total_venta"=>$sum_total_venta]);
    }
    public function reporte_venta_categoria(Request $request)
    {
        $vendedores=DB::table('tb_persona')->where('tipo_persona','Vendedor')->get();
        $vende = trim($request->get('searchVendedor'));

        $f1 = Carbon::now()->toDateString("FechaInicio");
        $f2 = Carbon::now()->toDateString("FechaFinal");

        $f1=$request->get('FechaInicio');
        $f2=$request->get('FechaFinal');

        $vendedor=DB::table('tb_persona')
            ->where(function($query) use ($vende){
                if ($vende) {
                    if ($vende != "") {
                         return $query->where('idpersona',$vende);
                    }
                }
            })
            ->where([
                    ['tipo_persona','Vendedor'],
                    ['estado','1']
                ])
            ->orderBy('idpersona')
            ->paginate(1);

            $noces=DB::table('tb_venta as v')
                ->join('tb_nota_credito as nc','nc.idventa','v.idventa')
                ->join('tb_detalle_noce as dn','dn.idnoce','nc.idnoce')
                ->join('tb_articulo as a','a.idarticulo','dn.idarticulo')
                ->join('tb_categoria as c','c.idcategoria','a.idcategoria')
                ->select('v.idvendedor','a.idcategoria',
                            DB::raw("SUM(dn.cantidad) AS cantidad"),
                            DB::raw("SUM(dn.cantidad * dn.precio_venta) AS neto")
                        )
                ->where(function($query) use ($f1,$f2,$vende){

                        if (($f1 != "") & ($f2 != "") & ($vende != ""))
                        {
                            return $query->WhereBetween('v.fecha_hora', [$f1,$f2])
                                        ->Where(function($q) use ($vende){
                                            $q->Where('v.idvendedor',$vende);
                                        });
                        }
                        else
                        {
                            return $query->whereMonth('v.fecha_hora', date('m'));
                        }
                })
                ->where([
                    ['v.estado','<>','Anulada'],
                    ['v.estado','<>','Eliminada']
                ])
                ->groupBy('v.idvendedor','a.idcategoria')
                ->get();

            $ventas=DB::table('tb_venta as v')
                ->join('tb_detalle_venta as dv','dv.idventa','v.idventa')
                ->join('tb_persona as p','p.idpersona','=','v.idvendedor')
                ->join('tb_articulo as a','a.idarticulo','dv.idarticulo')
                ->join('tb_categoria as c','c.idcategoria','a.idcategoria')
                ->select(/*'v.idvendedor','p.nombre as vendedor',*/'a.idcategoria','c.nombre as categorias',
                            DB::raw("SUM(dv.cantidad) AS cantidad"),
                            DB::raw("SUM(dv.cantidad * dv.precio_venta) AS neto")
                        )
                ->where(function($query) use ($vende,$f1,$f2){
                    if (($f1) & ($f2)) {
                        if (($f1 != "") & ($f2 != "") & ($vende != "")) {
                            return $query->WhereBetween('v.fecha_hora', [$f1,$f2])
                                            ->Where(function($q) use ($vende){
                                                $q->Where('v.idvendedor',$vende);
                                            });
                        }else{
                            if (($f1) & ($f2)) {
                                return $query->WhereBetween('v.fecha_hora', [$f1,$f2]);
                            }
                        }
                    }else{
                        if ($vende != "") {
                            return $query->where('v.idvendedor',$vende)
                                        ->where(function($q){
                                            $q->whereMonth('v.fecha_hora', date('m'));
                                        });
                        }else{
                            return $query->whereMonth('v.fecha_hora', date('m'));
                        }
                    }
                })
                ->where([
                    ['v.estado','<>','Anulada'],
                    ['v.estado','<>','Eliminada']
                ])
                ->groupBy(/*'v.idvendedor','p.nombre',*/'a.idcategoria','c.nombre')
                //->orderBy('v.idvendedor')
                ->orderBy('a.idcategoria')
                ->get();

            $sum_total = 0;
            $sum_neto = 0;
            $noce_sum_total = 0;
            $noce_sum_neto = 0;
            foreach ($noces as $no) {
                $noce_sum_total += $no->cantidad;
                $noce_sum_neto += $no->neto;
            }
            foreach ($ventas as $venta) {
                $sum_total += $venta->cantidad;
                $sum_neto += $venta->neto;
            }//,"detalles"=>$detalles
        return view('reporte.venta.venta-categoria.index',["ventas"=>$ventas,"noces"=>$noces,"vendedores"=>$vendedores,"sum_total"=>$sum_total,"sum_neto"=>$sum_neto,"noce_sum_total"=>$noce_sum_total,"noce_sum_neto"=>$noce_sum_neto,"vendedor"=>$vendedor]);
    }

    public function reporte_factura_anulada(Request $request)
    {
        $vendedores=DB::table('tb_persona')->where('tipo_persona','Vendedor')->get();
        $vende = trim($request->get('searchVendedor'));

        $f1 = Carbon::now()->toDateString("FechaInicio");
        $f2 = Carbon::now()->toDateString("FechaFinal");

        $f1=$request->get('FechaInicio');
        $f2=$request->get('FechaFinal');

        $muni = $request->get('municipio');

            $ventas=DB::table('tb_venta as v')
                ->join('tb_persona as p','v.idcliente','=','p.idpersona')
                ->join('tb_persona as p2','v.idvendedor','=','p2.idpersona')
                ->join('tb_detalle_venta as dv','v.idventa','=','dv.idventa')
                ->select('v.idventa','v.idvendedor','v.fecha_hora','v.idcliente','p.nombre','p2.nombre as vendedor','p.municipio','v.tipo_comprobante','v.serie_comprobante','v.num_comprobante','v.estado','v.total_venta')
                ->where(function($query) use ($vende,$muni,$f1,$f2){
                    if (($f1) & ($f2)) {
                        if (($f1 != "") & ($f2 != "") & ($vende != "")) {
                            return $query->WhereBetween('v.fecha_hora', [$f1,$f2])
                                            ->where(function($q) use ($vende,$muni){
                                                $q->Where('v.idvendedor',$vende);
                                            });
                        }else{
                            if (($f1 != "") & ($f2 != "")){
                                return $query->WhereBetween('v.fecha_hora', [$f1,$f2]);
                            }else{
                                return $query->whereMonth('v.fecha_hora', date('m'));
                            }
                        }
                    }
                    if ($vende) {
                        if ($vende != "") {
                             return $query->where('v.idvendedor',$vende)
                                        ->where(function($q){
                                            $q->whereMonth('v.fecha_hora', date('m'));
                                        });
                        }
                    }
                })
                ->groupBy('v.idventa','v.fecha_hora','p.nombre','v.idcliente','p2.nombre','p.municipio','v.tipo_comprobante','v.serie_comprobante','v.num_comprobante','v.estado','v.total_venta')
               ->where([
                    ['v.estado','=','Anulada']
                ])
                ->orderBy('idventa','desc')
                ->paginate(200);

            $sum_total_venta = 0;
            foreach ($ventas as $venta) {
                $sum_total_venta += $venta->total_venta;
            }
        return view('reporte.venta.facturas-anuladas.index',["ventas"=>$ventas,"vendedores"=>$vendedores,"sum_total_venta"=>$sum_total_venta]);
    }
    public function reporte_venta_detallada_vendedor(Request $request)
    {
        $vendedores=DB::table('tb_persona')
                ->where([
                    ['tipo_persona','Vendedor']/*,
                    ['estado','1']*/
                ])
        ->get();
        $vende = trim($request->get('searchVendedor'));



        $categorias=DB::table('tb_categoria')->where('condicion','=','1')->get();
        $cat = trim($request->get('searchCategoria'));

        /*$f1 = Carbon::now()->toDateString("FechaInicio");
        $f2 = Carbon::now()->toDateString("FechaFinal");*/

        $f1=$request->get('FechaInicio');
        $f2=$request->get('FechaFinal');

        $date = Carbon::now();

        if(($f1 != "") & ($f2 != "")){
            $date_1 = $request->get('FechaInicio');
            $date_2 = $request->get('FechaFinal');
        }else{
            $date_1 = $date->format('Y-m-01');
            $date_2 = $date;//$date->format('Y-m-d');
        }
        $vendedor=DB::table('tb_persona')
            ->where(function($query) use ($vende){
                if ($vende) {
                    if ($vende != "") {
                         return $query->where('idpersona',$vende);
                    }
                }
            })
            ->where([
                    ['tipo_persona','Vendedor'],
                    ['estado','1']
                ])
            ->paginate(1);

        $art_ventas=DB::table('tb_detalle_venta as dv')
            ->join('tb_venta as v','v.idventa','dv.idventa')
            ->join('tb_articulo as a','a.idarticulo','dv.idarticulo')
            ->join('tb_categoria as c','c.idcategoria','a.idcategoria')
            ->select('v.idvendedor','dv.idarticulo','a.nombre','c.nombre as categoria',DB::raw("sum(cantidad) as cantidad"))
            ->where(function($query) use ($cat,$vende,$f1,$f2,$date_1,$date_2){
	                if (($f1) & ($f2)) {
	                    if (($f1 != "") & ($f2 != "") & ($vende != "")) {
	                        return $query->WhereBetween('v.fecha_hora', [$f1,$f2])
	                                        ->where(function($q) use ($vende){
	                                            $q->Where('v.idvendedor',$vende);
	                                        });
	                    }else{
	                        if (($f1 != "") & ($f2 != "")){
	                            return $query->WhereBetween('v.fecha_hora', [$f1,$f2]);
	                        }else{
	                            return $query->whereMonth('v.fecha_hora', date('m'));
	                        }
	                    }
	                }
                    if (($date_1 != "") & ($date_2 != "")) {
                        return $query->where([
							                    ['v.fecha_hora','>=',$date_1],
							                    ['v.fecha_hora','<=',$date_2]
							                ]);
                    }
                    if ($vende) {
                        if ($vende != "") {
                             return $query->where('v.idvendedor',$vende)
                                        ->where(function($q){
                                            $q->whereMonth('v.fecha_hora', date('m'));
                                        });
                        }
                    }
                if($cat){
                    if ($cat != "") {
                        return $query->where('c.idcategoria',$cat);
                    }
                }
            })
            ->where([
                    ['v.estado','<>','Anulada'],
                    ['v.estado','<>','Eliminada']
                ])
            ->groupBy('v.idvendedor','dv.idarticulo','a.nombre','c.nombre')
            ->orderBy('cantidad','desc')
            //->paginate(25);
            ->get();
           // dd($art_ventas);
         return view('reporte.venta.detalle-venta-vendedor.index',compact('art_ventas','categorias','vendedores','vendedor'));
    }
    public function reporte_comision_vendedor(Request $request)
    {
        $var=DB::table('tb_variable')->first();

        $vendedores=DB::table('tb_persona')
            ->where([
                    ['idpersona','<>',225],
                    ['tipo_persona','Vendedor'],
                    ['estado','1']
                ])
            ->orderBy('idpersona')
            ->get();
        $vende = trim($request->get('searchVendedor'));

        $f1 = Carbon::now()->toDateString("FechaInicio");
        $f2 = Carbon::now()->toDateString("FechaFinal");
        $f1=$request->get('FechaInicio');
        $f2=$request->get('FechaFinal');

        $vendedor=DB::table('tb_persona')
            ->where(function($query) use ($vende){
                if ($vende) {
                    if ($vende != "") {
                        return $query->where('idpersona',$vende);
                    }
                }
            })
            ->where([
                    ['tipo_persona','Vendedor'],
                    ['estado','1']
                ])
            ->orderBy('idpersona')
            ->paginate(1);


        $var_vendedor=DB::table('tb_variable_vendedor')
            ->where(function($query) use ($vende,$f1,$f2){
                if ($vende) {
                    if ($vende != "") {
                        return $query->where('idvendedor',$vende)
                                ->where(function($q){
                                    $q->whereMonth('fecha', date('m'));
                                });
                    }
                }
                if (($f1) & ($f2)) {
                    if (($f1 != "") & ($f2 != "") & ($vende != "")) {
                        return $query->WhereBetween('fecha', [$f1,$f2])
                                    ->where(function($q) use ($vende){
                                        $q->Where('idvendedor',$vende);
                                    });
                    }elseif(($f1 != "") & ($f2 != "")){
                        return $query->WhereBetween('fecha', [$f1,$f2]);
                    }
                }else{
                    return $query->whereMonth('fecha', date('m'));
                }
            })
            ->orderBy('idvendedor')
            ->first();

        $num_clientes=DB::table('tb_venta as v')
            ->select(DB::raw("count(distinct(idcliente)) as clientes"))
            ->where(function($query) use ($vende,$f1,$f2){
                if ($vende) {
                    if ($vende != "") {
                        return $query->where('idvendedor',$vende)
                                ->where(function($q){
                                    $q->whereMonth('fecha_entrega', date('m'));
                                });
                    }
                }
                if (($f1) & ($f2)) {
                    if (($f1 != "") & ($f2 != "") & ($vende != "")) {
                        return $query->WhereBetween('fecha_entrega', [$f1,$f2])
                                    ->where(function($q) use ($vende){
                                        $q->Where('idvendedor',$vende);
                                    });
                    }elseif(($f1 != "") & ($f2 != "")){
                        return $query->WhereBetween('fecha_entrega', [$f1,$f2]);
                    }
                }else{
                    return $query->whereMonth('fecha_entrega', date('m'));
                }
            })
            ->where([
                    ['estado','<>','Anulada'],
                    ['estado','<>','Eliminada']
                ])
            ->first();

            $venta_vendedor=DB::table('tb_venta')
                ->select(DB::raw("SUM(total_venta - total_noce) as venta_total"))
                ->where(function($query) use ($vende,$f1,$f2){
                    if ($vende) {
                        if ($vende != "") {
                            return $query->where('idvendedor',$vende)
                                    ->where(function($q){
                                        $q->whereMonth('fecha_entrega', date('m'));
                                    });
                        }
                    }
                    if (($f1) & ($f2)) {
                        if (($f1 != "") & ($f2 != "") & ($vende != "")) {
                            return $query->WhereBetween('fecha_entrega', [$f1,$f2])
                                        ->where(function($q) use ($vende){
                                            $q->Where('idvendedor',$vende);
                                        });
                        }elseif(($f1 != "") & ($f2 != "")){
                            return $query->WhereBetween('fecha_entrega', [$f1,$f2]);
                        }
                    }else{
                        return $query->whereMonth('fecha_entrega', date('m'));
                    }
                })
                ->where([
                    ['estado','<>','Anulada'],
                    ['estado','<>','Eliminada']
                ])
            ->first();

            $ventas=DB::table('tb_venta as v')
                ->join('tb_persona as p','p.idpersona','v.idcliente')
                ->select('v.idventa','v.idvendedor','v.idcliente','p.nombre','v.serie_comprobante','v.num_comprobante','v.estado','v.total_venta','v.total_noce','v.fecha_entrega','v.fecha_pagada')
                ->where(function($query) use ($vende,$f1,$f2){
                    if ($vende) {
                        if ($vende != "") {
                            return $query->where('v.idvendedor',$vende)
                                        ->where(function($q){
                                            $q->whereMonth('v.fecha_pagada', date('m'));
                                        });
                        }
                    }
                    if (($f1) & ($f2)) {
                        if (($f1 != "") & ($f2 != "") & ($vende != "")) {
                            return $query->WhereBetween('fecha_pagada', [$f1,$f2])
                                        ->where(function($q) use ($vende){
                                            $q->Where('idvendedor',$vende);
                                        });
                        }elseif(($f1 != "") & ($f2 != "")){
                            return $query->WhereBetween('fecha_pagada', [$f1,$f2]);
                        }
                    }else{
                        return $query->whereMonth('fecha_pagada', date('m'));
                    }
                })
                ->groupBy('v.idventa','v.idvendedor','v.idcliente','p.nombre','v.serie_comprobante','v.num_comprobante','v.estado','v.total_venta','v.total_noce','v.fecha_entrega','v.fecha_pagada')
               ->where([
                    ['v.estado','=','Pagada'],
                    ['v.dias_pago','<=',4]
                ])
                ->orderBy('v.serie_comprobante')
                //->paginate(200);
                ->get();
           // dd($ventas);
            $sum_total_venta = 0;
            foreach ($ventas as $venta) {
                $sum_total_venta += $venta->total_venta - $venta->total_noce;
            }
        return view('reporte.venta.comisiones.index',compact('ventas','vendedores','sum_total_venta','var','num_clientes','venta_vendedor','vendedor','var_vendedor')/*,["ventas"=>$ventas,"vendedores"=>$vendedores,"sum_total_venta"=>$sum_total_venta]*/);
    }
}
