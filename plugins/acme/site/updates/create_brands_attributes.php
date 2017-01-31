<?php namespace Acme\Site\Updates;

use Illuminate\Support\Facades\DB;
use October\Rain\Database\Updates\Migration;
use Schema;

class CreateBrandsAttributesTable extends Migration
{
    public function up()
    {
        Schema::table('vojtasvoboda_brands_brands', function ($table)
        {
            $table->boolean('ceo')->default(false)->after('sort_order');
            $table->boolean('top')->default(false)->after('ceo');
        });
    }

    public function down()
    {
        DB::statement("SET foreign_key_checks = 0");
        Schema::table('vojtasvoboda_brands_brands', function ($table) {
            $table->dropColumn(['ceo', 'top']);
        });
        DB::statement("SET foreign_key_checks = 1");
    }
}
