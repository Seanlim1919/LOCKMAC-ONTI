<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            try {
                // Insert admin user
                $adminUserId = DB::table('users')->insertGetId([
                    'first_name' => 'Marylie',
                    'middle_name' => 'Lorzano',
                    'last_name' => 'Refereza',
                    'email' => 'marefereza@cspc.edu.ph',
                    'password' => '$2y$10$XAOHRuztBgo9QpxX7WQN0.4HtG3TK5UH/5QETp2458WCyxP0RZziy',
                    'phone_number' => '09915135750',
                    'gender' => 'female',
                    'date_of_birth' => '2003-08-01',
                    'role' => 'admin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $adminRfidCode = '43 18 C0 E4';

                if (DB::table('rfids')->where('rfid_code', $adminRfidCode)->exists()) {
                    Log::error('Duplicate RFID code detected', ['rfid' => $adminRfidCode]);
                    throw new QueryException('Duplicate RFID code detected.');
                }

                $adminRfidId = DB::table('rfids')->insertGetId([
                    'rfid_code' => $adminRfidCode,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('users')->where('id', $adminUserId)->update(['rfid_id' => $adminRfidId]);

                Log::info('Admin user and their RFID have been inserted successfully.');
            } catch (QueryException $e) {
                Log::error('Error inserting admin user or RFID', ['error' => $e->getMessage()]);
                throw $e; 
            }
        });
    }
}