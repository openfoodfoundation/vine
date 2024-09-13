<?php

use App\Models\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use League\ISO3166\ISO3166;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index('c_n');
            $table->string('alpha2')->index('c_a2');
            $table->string('alpha3')->index('c_a3');
            $table->string('numeric')->index('c_nu');
            $table->string('currency_code')->index('c_cc');
            $table->timestamps();
            $table->softDeletes();
        });

        $leagueISO = new ISO3166();
        $countries = $leagueISO->all();

        foreach ($countries as $country) {
            $name  = ucwords(strtolower(trim($country['name'])));
            $model = Country::where('name', $name)->first();

            if (!$model) {

                $model                = new Country();
                $model->name          = $name;
                $model->alpha2        = strtoupper($country['alpha2']);
                $model->alpha3        = strtoupper($country['alpha3']);
                $model->numeric       = trim($country['numeric']);
                $model->currency_code = $country['currency'][0];
                $model->save();
            }
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
