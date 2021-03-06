@extends ('layouts.admin')
@section('name', "Cambiar Precios de Articulos")
@section('content')
	<div class="row">
		@include('seguridad.precio_articulo.search')
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="table-responsive">
				<table class="table table-striped table-bordered table-condensed table-hover">
					<thead>
						<th width="5%">Codigo</th>
						<th width="50%">Nombre</th>
						<th width="5%">Stock</th>
						<th width="15%">Precio de Compra</th>
						<th width="15%">Precio de Venta</th>
						<th width="10%">Opcions</th>
					</thead>
					@foreach ($articulos as $art)
						<tr>
							<td align="center">{{ str_pad($art->idarticulo, 3, "0", STR_PAD_LEFT) }}</td>
							<td>{{ $art->nombre }}</td>
							<td align="center">{{ $art->stock }}</td>
							<td align="right">{{ number_format($art->precio_compra, 2, ',', '.') }}</td>
							<td align="right">{{ number_format($art->precio_venta, 2, ',', '.') }}</td>
							<td align="center">
								<a href="{{ URL::action('EditPrecioController@edit',$art->idarticulo) }}"><button class="btn btn-primary"><i class="fa fa-edit"></i> Editar</button></a>
							</td>
						</tr>
					@endforeach
				</table>
			</div>
			{{ $articulos->render() }}
		</div>
	</div>
@endsection