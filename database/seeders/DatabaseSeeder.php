<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table( 'payment_status' )->insert([
            'name' => 'activo'
        ]);

        DB::table( 'payment_status' )->insert([
            'name' => 'pendiente de confirmación de token'
        ]);

        DB::table( 'payment_status' )->insert([
            'name' => 'aceptado'
        ]);

        DB::table( 'payment_status' )->insert([
            'name' => 'rechazado'
        ]);


        #Token Statu
        DB::table( 'token_status' )->insert([
            'name' => 'activo'
        ]);

        DB::table( 'token_status' )->insert([
            'name' => 'cancelado'
        ]);

        DB::table( 'token_status' )->insert([
            'name' => 'vencido'
        ]);

        #Walle Statu
        DB::table( 'wallet_status' )->insert([
            'name' => 'activo'
        ]);

        DB::table( 'wallet_status' )->insert([
            'name' => 'pendiente de aprobación'
        ]);

        DB::table( 'wallet_status' )->insert([
            'name' => 'rechazado'
        ]);

        DB::table( 'wallet_status' )->insert([
            'name' => 'cancelada'
        ]);
    }
}
