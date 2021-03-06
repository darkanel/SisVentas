{!! Form::open(array('url'=>'reporte/almacen/producto-mas-vendido', 'method'=>'GET', 'autocomplete'=>'off', 'role'=>'search' )) !!}
<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
    <div class="input-group">
        <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
        </div>
            <input type="text" id="FechaInicio" name="FechaInicio" class="form-control pull-right" data-date-end-date="0d">
        <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
        </div>
            <input type="text" id="FechaFinal" name="FechaFinal" class="form-control pull-right" data-date-end-date="0d">
        <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
        </span>
    </div>
</div>
<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="input-group">
            <select name="searchCategoria" class="form-control" name="reload_categoria" id="reload_categoria">
                <option value="">Selecciona una Categoria</option>
                @foreach ($categorias as $cat)
                    <option value="{{ $cat->idcategoria }}">{{ $cat->nombre }}</option>
                @endforeach
            </select>
            <span class="input-group-btn">
                <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
            </span>
        </div>
    </div>
 {{ Form::close() }}