<?php

namespace SisVentas\Http\Controllers;

use Illuminate\Http\Request;

use SisVentas\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use SisVentas\Categoria;
use SisVentas\Http\Requests\CategoriaFormRequest;
use DB;

class CategoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        //dd($request->all());
        if ($request)
        {
            // Variable de busqueda por categoria dond trim quita los espacios en blanco en el inicio y el final
            $query=trim($request->get('searchText'));
            $categorias=DB::table('tb_categoria')
                ->where('nombre','LIKE','%'.$query.'%')
                ->where('condicion','=','1')
                ->orderBy('idcategoria','desc')
                ->paginate(10);
            return view('almacen.categoria.index',["categorias"=>$categorias,"searchText"=>$query]);
        }
    }

    public function create()
    {
        return view("almacen.categoria.create");
    }

    public function store(CategoriaFormRequest $request)
    {
        $categoria=new Categoria;
        $categoria->nombre=$request->get('nombre');
        $categoria->condicion='1';
        $categoria->save();
        //return response()->json($categoria);
        return Redirect::to('almacen/categoria');
    }

    public function show($id)
    {
        return view("almacen.categoria.show",["categoria"=>Categoria::findOrFail($id)]);
    }

    public function edit($id)
    {
        return view("almacen.categoria.edit",["categoria"=>Categoria::findOrFail($id)]);
    }

    public function update(CategoriaFormRequest $request, $id)
    {
        $categoria=Categoria::findOrFail($id);
        $categoria->nombre=$request->get('nombre');
        $categoria->update();
        return Redirect::to('almacen/categoria');
    }

    public function destroy($id)
    {
        $categoria=Categoria::findOrFail($id);
        $categoria->condicion='0';
        $categoria->delete();
        return Redirect::to('almacen/categoria');
    }
}
