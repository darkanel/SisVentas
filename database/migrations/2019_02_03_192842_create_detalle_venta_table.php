<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleVentaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_detalle_venta', function (Blueprint $table) {
            $table->increments('iddetalle_venta');
            $table->integer('idventa')->unsigned();
            $table->integer('idarticulo')->unsigned();
            $table->integer('cantidad');
            $table->integer('descuento');
            $table->decimal('precio_venta',11,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_detalle_venta');
    }
}
