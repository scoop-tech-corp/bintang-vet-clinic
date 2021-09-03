<?php

use Illuminate\Database\Seeder;

class DataDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('branches')->insert([
            'branch_code' => 'AS',
            'branch_name' => 'Alam Sutera',
            'address' => 'Ruko Spectra blok 23c no 19 Alam Sutera',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('branches')->insert([
            'branch_code' => 'KB',
            'branch_name' => 'Kebagusan',
            'address' => 'Jl. Kebagusan Raya no 48 Jaksel',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('branches')->insert([
            'branch_code' => 'TJ',
            'branch_name' => 'Tanjung Duren',
            'address' => 'Jl. Tanjung Duren Barat 1 no 19c Jakbar',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        //cabang alam sutera
        DB::table('users')->insert([
            'staffing_number' => '12345',
            'username' => 'budi',
            'fullname' => 'budi saputri',
            'email' => 'budi@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '081223456789',
            'role' => 'admin',
            'branch_id' => 1,
            'status' => '1',
            'created_by' => 'budi saputri',
            'created_at' => '2020-12-30',
        ]);

        DB::table('users')->insert([
            'staffing_number' => '12345',
            'username' => 'susi',
            'fullname' => 'susi saputri',
            'email' => 'susi@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '081223456788',
            'role' => 'resepsionis',
            'branch_id' => 1,
            'status' => '1',
            'created_by' => 'budi saputri',
            'created_at' => '2020-12-30',
        ]);

        DB::table('users')->insert([
            'staffing_number' => '12345',
            'username' => 'tono',
            'fullname' => 'tono saputri',
            'email' => 'tono@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '081223456787',
            'role' => 'dokter',
            'branch_id' => 1,
            'status' => '1',
            'created_by' => 'budi saputri',
            'created_at' => '2020-12-30',
        ]);

        DB::table('users')->insert([
            'staffing_number' => '12345',
            'username' => 'tona',
            'fullname' => 'tona saputri',
            'email' => 'tona@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '081223456888',
            'role' => 'dokter',
            'branch_id' => 1,
            'status' => '0',
            'created_by' => 'budi saputri',
            'created_at' => '2020-12-30',
        ]);

        //cabang kembangan
        DB::table('users')->insert([
            'staffing_number' => '12345',
            'username' => 'toto',
            'fullname' => 'toto saputri',
            'email' => 'toto@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '081223456779',
            'role' => 'admin',
            'branch_id' => 2,
            'status' => '1',
            'created_by' => 'budi saputri',
            'created_at' => '2020-12-30',
        ]);

        DB::table('users')->insert([
            'staffing_number' => '12345',
            'username' => 'susan',
            'fullname' => 'susan saputri',
            'email' => 'susan@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '081223456768',
            'role' => 'resepsionis',
            'branch_id' => 2,
            'status' => '1',
            'created_by' => 'budi saputri',
            'created_at' => '2020-12-30',
        ]);

        DB::table('users')->insert([
            'staffing_number' => '12345',
            'username' => 'deni',
            'fullname' => 'deni saputra',
            'email' => 'deni@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '081223456757',
            'role' => 'dokter',
            'branch_id' => 2,
            'status' => '1',
            'created_by' => 'budi saputri',
            'created_at' => '2020-12-30',
        ]);

        DB::table('users')->insert([
            'staffing_number' => '12345',
            'username' => 'dena',
            'fullname' => 'dena saputra',
            'email' => 'dena@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '081223456747',
            'role' => 'dokter',
            'branch_id' => 2,
            'status' => '0',
            'created_by' => 'budi saputri',
            'created_at' => '2020-12-30',
        ]);

        //cabang tanjung duren
        DB::table('users')->insert([
            'staffing_number' => '12345',
            'username' => 'tati',
            'fullname' => 'tati saputri',
            'email' => 'tati@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '081223456749',
            'role' => 'admin',
            'branch_id' => 3,
            'status' => '1',
            'created_by' => 'budi saputri',
            'created_at' => '2020-12-30',
        ]);

        DB::table('users')->insert([
            'staffing_number' => '12345',
            'username' => 'sasa',
            'fullname' => 'sasa saputri',
            'email' => 'sasa@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '081223456778',
            'role' => 'resepsionis',
            'branch_id' => 3,
            'status' => '1',
            'created_by' => 'budi saputri',
            'created_at' => '2020-12-30',
        ]);

        DB::table('users')->insert([
            'staffing_number' => '12345',
            'username' => 'desi',
            'fullname' => 'desi saputri',
            'email' => 'desi@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '081223456777',
            'role' => 'dokter',
            'branch_id' => 3,
            'status' => '1',
            'created_by' => 'budi saputri',
            'created_at' => '2020-12-30',
        ]);

        DB::table('users')->insert([
            'staffing_number' => '12345',
            'username' => 'desna',
            'fullname' => 'desna saputri',
            'email' => 'desna@gmail.com',
            'password' => bcrypt('password'),
            'phone_number' => '081223456767',
            'role' => 'dokter',
            'branch_id' => 3,
            'status' => '0',
            'created_by' => 'budi saputri',
            'created_at' => '2020-12-30',
        ]);

        //pasien
        DB::table('patients')->insert([
            'branch_id' => 1,
            'id_member' => 'BVC-P-AS-0001',
            'pet_category' => 'kucing',
            'pet_name' => 'kuki',
            'pet_gender' => 'betina',
            'pet_year_age' => 2,
            'pet_month_age' => 10,
            'owner_name' => 'agus',
            'owner_address' => 'tangerang selatan',
            'owner_phone_number' => '081234560987',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('patients')->insert([
            'branch_id' => 1,
            'id_member' => 'BVC-P-AS-0002',
            'pet_category' => 'monyet',
            'pet_name' => 'kimbo',
            'pet_gender' => 'jantan',
            'pet_year_age' => 5,
            'pet_month_age' => 10,
            'owner_name' => 'yudi',
            'owner_address' => 'duri',
            'owner_phone_number' => '0812345609009',
            'user_id' => '1',
            'created_at' => '2021-03-20',
        ]);

        DB::table('patients')->insert([
            'branch_id' => 1,
            'id_member' => 'BVC-P-AS-0003',
            'pet_category' => 'anjing',
            'pet_name' => 'sisi',
            'pet_gender' => 'betina',
            'pet_year_age' => 6,
            'pet_month_age' => 8,
            'owner_name' => 'cindy',
            'owner_address' => 'alam sutera',
            'owner_phone_number' => '081234560987',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('patients')->insert([
            'branch_id' => 3,
            'id_member' => 'BVC-P-TJ-0001',
            'pet_category' => 'anjing',
            'pet_name' => 'rambo',
            'pet_gender' => 'jantan',
            'pet_year_age' => 2,
            'pet_month_age' => 10,
            'owner_name' => 'tina',
            'owner_address' => 'lebak bulus',
            'owner_phone_number' => '081234560988',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('patients')->insert([
            'branch_id' => 3,
            'id_member' => 'BVC-P-TJ-0002',
            'pet_category' => 'kucing',
            'pet_name' => 'butet',
            'pet_gender' => 'jantan',
            'pet_year_age' => 10,
            'pet_month_age' => 10,
            'owner_name' => 'raka',
            'owner_address' => 'kemanggisan',
            'owner_phone_number' => '081234560988',
            'user_id' => '1',
            'created_at' => '2021-01-12',
        ]);

        DB::table('patients')->insert([
            'branch_id' => 3,
            'id_member' => 'BVC-P-TJ-0003',
            'pet_category' => 'anjing',
            'pet_name' => 'godart',
            'pet_gender' => 'jantan',
            'pet_year_age' => 8,
            'pet_month_age' => 10,
            'owner_name' => 'ricky',
            'owner_address' => 'ilir',
            'owner_phone_number' => '081234560928',
            'user_id' => '1',
            'created_at' => '2021-02-18',
        ]);

        DB::table('patients')->insert([
            'branch_id' => 2,
            'id_member' => 'BVC-P-KB-0001',
            'pet_category' => 'kucing',
            'pet_name' => 'tabi',
            'pet_gender' => 'tidak diketahui',
            'pet_year_age' => 2,
            'pet_month_age' => 10,
            'owner_name' => 'tono',
            'owner_address' => 'pondok indah',
            'owner_phone_number' => '081234560989',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('patients')->insert([
            'branch_id' => 2,
            'id_member' => 'BVC-P-KB-0002',
            'pet_category' => 'kucing',
            'pet_name' => 'mpus',
            'pet_gender' => 'betina',
            'pet_year_age' => 11,
            'pet_month_age' => 10,
            'owner_name' => 'lisa',
            'owner_address' => 'pondok indah',
            'owner_phone_number' => '081234560989',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('patients')->insert([
            'branch_id' => 2,
            'id_member' => 'BVC-P-KB-0003',
            'pet_category' => 'ikan',
            'pet_name' => 'woofy',
            'pet_gender' => 'tidak diketahui',
            'pet_year_age' => 6,
            'pet_month_age' => 10,
            'owner_name' => 'tono',
            'owner_address' => 'pondok indah',
            'owner_phone_number' => '081234560989',
            'user_id' => '1',
            'created_at' => '2021-04-22',
        ]);

        //category_item
        DB::table('category_item')->insert([
            'category_name' => 'Git',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('category_item')->insert([
            'category_name' => 'Antibiotik',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('category_item')->insert([
            'category_name' => 'Suplemen',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('category_item')->insert([
            'category_name' => 'Antiseptik & Densifektan',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('category_item')->insert([
            'category_name' => 'Anti Radang',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('category_item')->insert([
            'category_name' => 'Parasit',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('category_item')->insert([
            'category_name' => 'Jamur',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        //unit_item
        DB::table('unit_item')->insert([
            'unit_name' => 'Pcs',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('unit_item')->insert([
            'unit_name' => 'Box',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('unit_item')->insert([
            'unit_name' => 'Strip',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('unit_item')->insert([
            'unit_name' => 'Botol',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        //list_of_items
        //cabang alam sutera
        DB::table('list_of_items')->insert([
            'item_name' => 'Vosea',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '1',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Kaotin',
            'total_item' => '30',
            'unit_item_id' => '4',
            'category_item_id' => '1',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Loperamide',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '1',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Metronidazole',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '2',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Cefixime',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '2',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Doxy',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '2',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Zinc',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '3',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Imboost',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '3',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Fish Oil',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '3',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Betadine',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '4',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Alkohol',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '4',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'H2O2',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '4',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Metil Prednisolone',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '5',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Dexa',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '5',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Revolution',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '6',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Caniverm',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '6',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Itraconazole',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '7',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'GRISEOFULVIN',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '7',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'TEOPILIN',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '7',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        //cabang kembangan
        DB::table('list_of_items')->insert([
            'item_name' => 'Vosea',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '1',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Kaotin',
            'total_item' => '30',
            'unit_item_id' => '4',
            'category_item_id' => '1',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Loperamide',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '1',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Metronidazole',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '2',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Cefixime',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '2',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Doxy',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '2',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Zinc',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '3',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Imboost',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '3',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Fish Oil',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '3',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Betadine',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '4',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Alkohol',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '4',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'H2O2',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '4',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Metil Prednisolone',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '5',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Dexa',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '5',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Revolution',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '6',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Caniverm',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '6',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Itraconazole',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '7',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'GRISEOFULVIN',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '7',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'TEOPILIN',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '7',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        //cabang tanjung duren
        DB::table('list_of_items')->insert([
            'item_name' => 'Vosea',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '1',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Kaotin',
            'total_item' => '30',
            'unit_item_id' => '4',
            'category_item_id' => '1',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Loperamide',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '1',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Metronidazole',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '2',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Cefixime',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '2',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Doxy',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '2',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Zinc',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '3',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Imboost',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '3',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Fish Oil',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '3',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Betadine',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '4',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Alkohol',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '4',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'H2O2',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '4',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Metil Prednisolone',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '5',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Dexa',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '5',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Revolution',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '6',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Caniverm',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '6',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'Itraconazole',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '7',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'GRISEOFULVIN',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '7',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_items')->insert([
            'item_name' => 'TEOPILIN',
            'total_item' => '30',
            'unit_item_id' => '3',
            'category_item_id' => '7',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        //service_categories
        DB::table('service_categories')->insert([
            'category_name' => 'Operasi',
            'user_id' => '1',
            'created_at' => '2020-12-29',
        ]);

        DB::table('service_categories')->insert([
            'category_name' => 'Tindakan 1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('service_categories')->insert([
            'category_name' => 'Tindakan 2',
            'user_id' => '1',
            'created_at' => '2020-12-31',
        ]);

        //list_of_services
        DB::table('list_of_services')->insert([
            'service_name' => 'rawat jalan',
            'service_category_id' => '2',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_services')->insert([
            'service_name' => 'rawat inap',
            'service_category_id' => '3',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_services')->insert([
            'service_name' => 'operasi caesar',
            'service_category_id' => '1',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('list_of_services')->insert([
            'service_name' => 'operasi biasa',
            'service_category_id' => '1',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        //price_services
        DB::table('price_services')->insert([
            'list_of_services_id' => '1',
            'selling_price' => 100000,
            'capital_price' => 0,
            'doctor_fee' => 70000,
            'petshop_fee' => 30000,
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('price_services')->insert([
            'list_of_services_id' => '2',
            'selling_price' => 120000,
            'capital_price' => 20000,
            'doctor_fee' => 70000,
            'petshop_fee' => 30000,
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('price_services')->insert([
            'list_of_services_id' => '3',
            'selling_price' => 200000,
            'capital_price' => 60000,
            'doctor_fee' => 70000,
            'petshop_fee' => 70000,
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        //price_items
        // DB::table('price_items')->insert([
        //     'list_of_items_id' => '1',
        //     'selling_price' => 200000,
        //     'capital_price' => 60000,
        //     'doctor_fee' => 70000,
        //     'petshop_fee' => 70000,
        //     'user_id' => '1',
        //     'created_at' => '2020-12-30',
        // ]);

        // DB::table('price_items')->insert([
        //     'list_of_items_id' => '2',
        //     'selling_price' => 200000,
        //     'capital_price' => 60000,
        //     'doctor_fee' => 70000,
        //     'petshop_fee' => 70000,
        //     'user_id' => '1',
        //     'created_at' => '2020-12-30',
        // ]);

        // DB::table('price_items')->insert([
        //     'list_of_items_id' => '3',
        //     'selling_price' => 200000,
        //     'capital_price' => 60000,
        //     'doctor_fee' => 70000,
        //     'petshop_fee' => 70000,
        //     'user_id' => '1',
        //     'created_at' => '2020-12-30',
        // ]);

        DB::table('price_items')->insert([
          'list_of_items_id'=>1,
          'selling_price'=>'900000.00',
          'capital_price'=>'300000.00',
          'doctor_fee'=>'300000.00',
          'petshop_fee'=>'300000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>2,
          'selling_price'=>'800000.00',
          'capital_price'=>'100000.00',
          'doctor_fee'=>'300000.00',
          'petshop_fee'=>'400000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>3,
          'selling_price'=>'700000.00',
          'capital_price'=>'150000.00',
          'doctor_fee'=>'300000.00',
          'petshop_fee'=>'250000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>4,
          'selling_price'=>'600000.00',
          'capital_price'=>'200000.00',
          'doctor_fee'=>'200000.00',
          'petshop_fee'=>'200000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>5,
          'selling_price'=>'500000.00',
          'capital_price'=>'100000.00',
          'doctor_fee'=>'125000.00',
          'petshop_fee'=>'275000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>6,
          'selling_price'=>'400000.00',
          'capital_price'=>'50000.00',
          'doctor_fee'=>'150000.00',
          'petshop_fee'=>'200000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>7,
          'selling_price'=>'300000.00',
          'capital_price'=>'100000.00',
          'doctor_fee'=>'100000.00',
          'petshop_fee'=>'100000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>8,
          'selling_price'=>'200000.00',
          'capital_price'=>'50000.00',
          'doctor_fee'=>'50000.00',
          'petshop_fee'=>'100000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>9,
          'selling_price'=>'100000.00',
          'capital_price'=>'25000.00',
          'doctor_fee'=>'25000.00',
          'petshop_fee'=>'50000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>10,
          'selling_price'=>'90000.00',
          'capital_price'=>'30000.00',
          'doctor_fee'=>'30000.00',
          'petshop_fee'=>'30000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>11,
          'selling_price'=>'80000.00',
          'capital_price'=>'20000.00',
          'doctor_fee'=>'30000.00',
          'petshop_fee'=>'30000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>12,
          'selling_price'=>'70000.00',
          'capital_price'=>'15000.00',
          'doctor_fee'=>'25000.00',
          'petshop_fee'=>'30000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>13,
          'selling_price'=>'60000.00',
          'capital_price'=>'20000.00',
          'doctor_fee'=>'20000.00',
          'petshop_fee'=>'20000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>14,
          'selling_price'=>'500000.00',
          'capital_price'=>'200000.00',
          'doctor_fee'=>'100000.00',
          'petshop_fee'=>'200000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>15,
          'selling_price'=>'50000.00',
          'capital_price'=>'15000.00',
          'doctor_fee'=>'10000.00',
          'petshop_fee'=>'25000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>16,
          'selling_price'=>'40000.00',
          'capital_price'=>'10000.00',
          'doctor_fee'=>'10000.00',
          'petshop_fee'=>'20000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>17,
          'selling_price'=>'30000.00',
          'capital_price'=>'5000.00',
          'doctor_fee'=>'10000.00',
          'petshop_fee'=>'15000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>18,
          'selling_price'=>'20000.00',
          'capital_price'=>'5000.00',
          'doctor_fee'=>'5000.00',
          'petshop_fee'=>'10000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>19,
          'selling_price'=>'10000.00',
          'capital_price'=>'2500.00',
          'doctor_fee'=>'2500.00',
          'petshop_fee'=>'5000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>20,
          'selling_price'=>'20000.00',
          'capital_price'=>'5000.00',
          'doctor_fee'=>'5000.00',
          'petshop_fee'=>'10000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>21,
          'selling_price'=>'30000.00',
          'capital_price'=>'10000.00',
          'doctor_fee'=>'10000.00',
          'petshop_fee'=>'10000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>22,
          'selling_price'=>'40000.00',
          'capital_price'=>'10000.00',
          'doctor_fee'=>'10000.00',
          'petshop_fee'=>'20000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>23,
          'selling_price'=>'50000.00',
          'capital_price'=>'15000.00',
          'doctor_fee'=>'10000.00',
          'petshop_fee'=>'25000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>24,
          'selling_price'=>'60000.00',
          'capital_price'=>'20000.00',
          'doctor_fee'=>'20000.00',
          'petshop_fee'=>'20000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>25,
          'selling_price'=>'80000.00',
          'capital_price'=>'20000.00',
          'doctor_fee'=>'20000.00',
          'petshop_fee'=>'40000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>26,
          'selling_price'=>'70000.00',
          'capital_price'=>'30000.00',
          'doctor_fee'=>'20000.00',
          'petshop_fee'=>'20000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>27,
          'selling_price'=>'80000.00',
          'capital_price'=>'25000.00',
          'doctor_fee'=>'15000.00',
          'petshop_fee'=>'40000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>28,
          'selling_price'=>'90000.00',
          'capital_price'=>'30000.00',
          'doctor_fee'=>'30000.00',
          'petshop_fee'=>'30000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>29,
          'selling_price'=>'10000.00',
          'capital_price'=>'3000.00',
          'doctor_fee'=>'3000.00',
          'petshop_fee'=>'4000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>30,
          'selling_price'=>'20000.00',
          'capital_price'=>'5000.00',
          'doctor_fee'=>'5000.00',
          'petshop_fee'=>'10000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>31,
          'selling_price'=>'50000.00',
          'capital_price'=>'15000.00',
          'doctor_fee'=>'25000.00',
          'petshop_fee'=>'10000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>32,
          'selling_price'=>'60000.00',
          'capital_price'=>'20000.00',
          'doctor_fee'=>'20000.00',
          'petshop_fee'=>'20000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>33,
          'selling_price'=>'800000.00',
          'capital_price'=>'200000.00',
          'doctor_fee'=>'200000.00',
          'petshop_fee'=>'400000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>34,
          'selling_price'=>'70000.00',
          'capital_price'=>'20000.00',
          'doctor_fee'=>'20000.00',
          'petshop_fee'=>'30000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>35,
          'selling_price'=>'75000.00',
          'capital_price'=>'25000.00',
          'doctor_fee'=>'25000.00',
          'petshop_fee'=>'25000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>36,
          'selling_price'=>'65000.00',
          'capital_price'=>'20000.00',
          'doctor_fee'=>'15000.00',
          'petshop_fee'=>'30000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>37,
          'selling_price'=>'87000.00',
          'capital_price'=>'29000.00',
          'doctor_fee'=>'29000.00',
          'petshop_fee'=>'29000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>38,
          'selling_price'=>'85000.00',
          'capital_price'=>'20000.00',
          'doctor_fee'=>'25000.00',
          'petshop_fee'=>'40000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>39,
          'selling_price'=>'65000.00',
          'capital_price'=>'20000.00',
          'doctor_fee'=>'25000.00',
          'petshop_fee'=>'20000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>40,
          'selling_price'=>'80000.00',
          'capital_price'=>'20000.00',
          'doctor_fee'=>'25000.00',
          'petshop_fee'=>'35000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>41,
          'selling_price'=>'9000.00',
          'capital_price'=>'3000.00',
          'doctor_fee'=>'3000.00',
          'petshop_fee'=>'3000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>42,
          'selling_price'=>'36000.00',
          'capital_price'=>'12000.00',
          'doctor_fee'=>'12000.00',
          'petshop_fee'=>'12000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>43,
          'selling_price'=>'545000.00',
          'capital_price'=>'100000.00',
          'doctor_fee'=>'245000.00',
          'petshop_fee'=>'200000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>44,
          'selling_price'=>'66000.00',
          'capital_price'=>'22000.00',
          'doctor_fee'=>'22000.00',
          'petshop_fee'=>'22000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>45,
          'selling_price'=>'50000.00',
          'capital_price'=>'20000.00',
          'doctor_fee'=>'20000.00',
          'petshop_fee'=>'10000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>46,
          'selling_price'=>'650000.00',
          'capital_price'=>'200000.00',
          'doctor_fee'=>'200000.00',
          'petshop_fee'=>'250000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>47,
          'selling_price'=>'98000.00',
          'capital_price'=>'30000.00',
          'doctor_fee'=>'38000.00',
          'petshop_fee'=>'30000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>48,
          'selling_price'=>'78000.00',
          'capital_price'=>'26000.00',
          'doctor_fee'=>'26000.00',
          'petshop_fee'=>'26000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>49,
          'selling_price'=>'81000.00',
          'capital_price'=>'27000.00',
          'doctor_fee'=>'27000.00',
          'petshop_fee'=>'27000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>50,
          'selling_price'=>'65000.00',
          'capital_price'=>'20000.00',
          'doctor_fee'=>'20000.00',
          'petshop_fee'=>'25000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>51,
          'selling_price'=>'960000.00',
          'capital_price'=>'320000.00',
          'doctor_fee'=>'320000.00',
          'petshop_fee'=>'320000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>52,
          'selling_price'=>'850000.00',
          'capital_price'=>'300000.00',
          'doctor_fee'=>'250000.00',
          'petshop_fee'=>'300000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>53,
          'selling_price'=>'622000.00',
          'capital_price'=>'200000.00',
          'doctor_fee'=>'200000.00',
          'petshop_fee'=>'222000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>54,
          'selling_price'=>'540000.00',
          'capital_price'=>'180000.00',
          'doctor_fee'=>'180000.00',
          'petshop_fee'=>'180000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>55,
          'selling_price'=>'540000.00',
          'capital_price'=>'180000.00',
          'doctor_fee'=>'180000.00',
          'petshop_fee'=>'180000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>56,
          'selling_price'=>'870000.00',
          'capital_price'=>'290000.00',
          'doctor_fee'=>'290000.00',
          'petshop_fee'=>'290000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

          DB::table('price_items')->insert([
          'list_of_items_id'=>57,
          'selling_price'=>'120000.00',
          'capital_price'=>'40000.00',
          'doctor_fee'=>'40000.00',
          'petshop_fee'=>'40000.00',
          'isDeleted'=>0,
          'user_id'=>1,
          'user_update_id'=>NULL,
          'deleted_by'=>NULL,
          'deleted_at'=>NULL,
          'created_at'=>'2021-07-27 06:05:09',
          'updated_at'=>'2021-07-27 06:05:09'
          ] );

        //registrations
        DB::table('registrations')->insert([
            'id_number' => 'BVC-RP-TJ-0001',
            'patient_id' => '4',
            'complaint' => 'pilek',
            'registrant' => 'agus',
            'user_id' => '1',
            'doctor_user_id' => '3',
            'acceptance_status' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('registrations')->insert([
            'id_number' => 'BVC-RP-KB-0001',
            'patient_id' => '7',
            'complaint' => 'gatal-gatal',
            'registrant' => 'kuncoro',
            'user_id' => '1',
            'doctor_user_id' => '4',
            'acceptance_status' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('registrations')->insert([
            'id_number' => 'BVC-RP-AS-0001',
            'patient_id' => '1',
            'complaint' => 'batuk',
            'registrant' => 'supri',
            'user_id' => '1',
            'doctor_user_id' => '7',
            'acceptance_status' => '2',
            'created_at' => '2020-12-30',
        ]);

        DB::table('registrations')->insert([
            'id_number' => 'BVC-RP-AS-0002',
            'patient_id' => '1',
            'complaint' => 'diare',
            'registrant' => 'sartoni',
            'user_id' => '1',
            'doctor_user_id' => '3',
            'acceptance_status' => '3',
            'created_at' => '2020-12-30',
        ]);

        //hasil pemeriksaan
        // DB::table('check_up_results')->insert([
        //     'patient_registration_id' => '2',
        //     'anamnesa' => 'ini adalah bentuk dari anamnesa',
        //     'sign' => 'ini adalah bentuk dari sign',
        //     'diagnosa' => 'ini adalah bentuk dari diagnosa',
        //     'status_outpatient_inpatient' => '0',
        //     'status_finish' => '1',
        //     'status_paid_off' => '0',
        //     'user_id' => '1',
        //     'created_at' => '2021-07-22',
        // ]);

        //     DB::table('check_up_results')->insert([
        //         'patient_registration_id' => '1',
        //         'anamnesa' => 'Ini adalah contoh anamnesa',
        //         'sign' => 'Ini adalah contoh sign',
        //         'diagnosa' => 'Ini adalah contoh diagnosa',
        //         'status_outpatient_inpatient' => '0',
        //         'status_finish' => '1',
        //         'status_paid_off' => '0',
        //         'user_id' => '1',
        //         'created_at' => '2021-02-26',
        //     ]);

        // //     // DB::table('check_up_results')->insert([
        // //     //     'patient_registration_id' => '4',
        // //     //     'anamnesa' => 'Ini adalah contoh anamnesa',
        // //     //     'sign' => 'Ini adalah contoh sign',
        // //     //     'diagnosa' => 'Ini adalah contoh diagnosa',
        // //     //     'status_outpatient_inpatient' => '0',
        // //     //     'status_finish' => '1',
        // //     //     'status_paid_off' => '0',
        // //     //     'user_id' => '1',
        // //     //     'created_at' => '2021-02-26',
        // //     // ]);



        //     // //detail item patient
        // DB::table('detail_item_patients')->insert([
        //   'check_up_result_id'=>1,
        //   'price_item_id'=>2,
        //   'quantity'=>1,
        //   'price_overall'=>'200000.00',
        //   'isDeleted'=>0,
        //   'user_id'=>1,
        //   'user_update_id'=>NULL,
        //   'deleted_by'=>NULL,
        //   'deleted_at'=>NULL,
        //   'created_at'=>'2021-07-21 17:00:00',
        //   'updated_at'=>NULL,
        //   'detail_medicine_group_id'=>1
        //   ] );

        //   DB::table('detail_item_patients')->insert([
        //   'check_up_result_id'=>1,
        //   'price_item_id'=>3,
        //   'quantity'=>1,
        //   'price_overall'=>'200000.00',
        //   'isDeleted'=>0,
        //   'user_id'=>1,
        //   'user_update_id'=>NULL,
        //   'deleted_by'=>NULL,
        //   'deleted_at'=>NULL,
        //   'created_at'=>'2021-07-21 17:00:00',
        //   'updated_at'=>NULL,
        //   'detail_medicine_group_id'=>1
        //   ] );

        //   DB::table('detail_item_patients')->insert([
        //   'check_up_result_id'=>1,
        //   'price_item_id'=>1,
        //   'quantity'=>2,
        //   'price_overall'=>'400000.00',
        //   'isDeleted'=>0,
        //   'user_id'=>1,
        //   'user_update_id'=>NULL,
        //   'deleted_by'=>NULL,
        //   'deleted_at'=>NULL,
        //   'created_at'=>'2021-07-21 17:00:00',
        //   'updated_at'=>NULL,
        //   'detail_medicine_group_id'=>2
        //   ] );

        //   DB::table('detail_item_patients')->insert([
        //   'check_up_result_id'=>1,
        //   'price_item_id'=>2,
        //   'quantity'=>1,
        //   'price_overall'=>'200000.00',
        //   'isDeleted'=>0,
        //   'user_id'=>1,
        //   'user_update_id'=>NULL,
        //   'deleted_by'=>NULL,
        //   'deleted_at'=>NULL,
        //   'created_at'=>'2021-07-21 17:00:00',
        //   'updated_at'=>NULL,
        //   'detail_medicine_group_id'=>2
        //   ] );

        //   DB::table('detail_item_patients')->insert([
        //   'check_up_result_id'=>2,
        //   'price_item_id'=>2,
        //   'quantity'=>1,
        //   'price_overall'=>'200000.00',
        //   'isDeleted'=>0,
        //   'user_id'=>1,
        //   'user_update_id'=>1,
        //   'deleted_by'=>NULL,
        //   'deleted_at'=>NULL,
        //   'created_at'=>'2021-07-23 09:44:19',
        //   'updated_at'=>'2021-07-23 09:53:47',
        //   'detail_medicine_group_id'=>3
        //   ] );

        //   DB::table('detail_item_patients')->insert([
        //   'check_up_result_id'=>2,
        //   'price_item_id'=>3,
        //   'quantity'=>1,
        //   'price_overall'=>'200000.00',
        //   'isDeleted'=>0,
        //   'user_id'=>1,
        //   'user_update_id'=>1,
        //   'deleted_by'=>NULL,
        //   'deleted_at'=>NULL,
        //   'created_at'=>'2021-07-23 09:44:19',
        //   'updated_at'=>'2021-07-23 09:53:47',
        //   'detail_medicine_group_id'=>3
        //   ] );

        //   DB::table('detail_item_patients')->insert([
        //   'check_up_result_id'=>2,
        //   'price_item_id'=>2,
        //   'quantity'=>1,
        //   'price_overall'=>'200000.00',
        //   'isDeleted'=>0,
        //   'user_id'=>1,
        //   'user_update_id'=>1,
        //   'deleted_by'=>NULL,
        //   'deleted_at'=>NULL,
        //   'created_at'=>'2021-07-23 09:44:19',
        //   'updated_at'=>'2021-07-23 09:53:48',
        //   'detail_medicine_group_id'=>4
        //   ] );

        //   DB::table('detail_item_patients')->insert([
        //   'check_up_result_id'=>2,
        //   'price_item_id'=>1,
        //   'quantity'=>1,
        //   'price_overall'=>'200000.00',
        //   'isDeleted'=>0,
        //   'user_id'=>1,
        //   'user_update_id'=>1,
        //   'deleted_by'=>NULL,
        //   'deleted_at'=>NULL,
        //   'created_at'=>'2021-07-23 09:44:19',
        //   'updated_at'=>'2021-07-23 09:53:48',
        //   'detail_medicine_group_id'=>4
        //   ] );

        //   DB::table('detail_item_patients')->insert([
        //   'check_up_result_id'=>2,
        //   'price_item_id'=>3,
        //   'quantity'=>1,
        //   'price_overall'=>'200000.00',
        //   'isDeleted'=>0,
        //   'user_id'=>1,
        //   'user_update_id'=>NULL,
        //   'deleted_by'=>NULL,
        //   'deleted_at'=>NULL,
        //   'created_at'=>'2021-07-23 09:53:47',
        //   'updated_at'=>'2021-07-23 09:53:47',
        //   'detail_medicine_group_id'=>4
        //   ] );

        // //     //detail_medicine_group_check_up_results
      //   DB::table('detail_medicine_group_check_up_results')->insert([
      //       'check_up_result_id' => '1',
      //       'medicine_group_id' => '4',
      //       'status_paid_off' => '0',
      //       'user_id' => '1',
      //       'created_at' => '2021-07-22',
      //   ]);

      //   DB::table('detail_medicine_group_check_up_results')->insert([
      //       'check_up_result_id' => '1',
      //       'medicine_group_id' => '4',
      //       'status_paid_off' => '0',
      //       'user_id' => '1',
      //       'created_at' => '2021-07-22',
      //   ]);

      //   DB::table('detail_medicine_group_check_up_results')->insert([
      //     'check_up_result_id' => '2',
      //     'medicine_group_id' => '4',
      //     'status_paid_off' => '0',
      //     'user_id' => '1',
      //     'created_at' => '2021-07-22',
      // ]);

      // DB::table('detail_medicine_group_check_up_results')->insert([
      //     'check_up_result_id' => '2',
      //     'medicine_group_id' => '1',
      //     'status_paid_off' => '0',
      //     'user_id' => '1',
      //     'created_at' => '2021-07-22',
      // ]);

      //   // //     // // //detail service patient
      //   DB::table('detail_service_patients')->insert([
      //       'check_up_result_id' => '1',
      //       'price_service_id' => '2',
      //       'quantity' => '1',
      //       'price_overall' => '120000',
      //       'status_paid_off' => '0',
      //       'user_id' => '1',
      //       'created_at' => '2021-07-22',
      //   ]);

      //   DB::table('detail_service_patients')->insert([
      //     'check_up_result_id' => '2',
      //     'price_service_id' => '2',
      //     'quantity' => '1',
      //     'price_overall' => '120000',
      //     'status_paid_off' => '0',
      //     'user_id' => '1',
      //     'created_at' => '2021-07-22',
      // ]);

      //   //images_check_up_results
      //   DB::table('images_check_up_results')->insert([
      //       'check_up_result_id' => '1',
      //       'image' => '/image_check_up_result/gRLBl3DHHElcHjwmk4jgdd7orpcMAwWpAousdO0I.jpg',
      //       'user_id' => '1',
      //       'created_at' => '2021-07-22',
      //   ]);

        //medicine group

        DB::table('medicine_groups')->insert([
            'group_name' => 'Anti Diare',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2021-07-23',
        ]);

        DB::table('medicine_groups')->insert([
            'group_name' => 'Pasca Operasi',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2021-07-23',
        ]);

        DB::table('medicine_groups')->insert([
            'group_name' => 'Pasca Hamil',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2021-07-23',
        ]);

        DB::table('medicine_groups')->insert([
            'group_name' => 'Paket Pilek',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2021-07-23',
        ]);

        DB::table('medicine_groups')->insert([
            'group_name' => 'Paket Tetanus',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2021-07-23',
        ]);

        DB::table('medicine_groups')->insert([
            'group_name' => 'Anti Tetanus',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2021-07-23',
        ]);

        DB::table('medicine_groups')->insert([
            'group_name' => 'Imunitas',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2021-07-23',
        ]);

        DB::table('medicine_groups')->insert([
            'group_name' => 'Anti Diare',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2021-07-23',
        ]);

        DB::table('medicine_groups')->insert([
            'group_name' => 'Imunitas',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2021-07-23',
        ]);

        DB::table('medicine_groups')->insert([
            'group_name' => 'Anti Diare',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2021-07-23',
        ]);

        DB::table('medicine_groups')->insert([
            'group_name' => 'Anti Radang',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2021-07-23',
        ]);

        DB::table('medicine_groups')->insert([
            'group_name' => 'Anti Parvo',
            'branch_id' => '3',
            'user_id' => '1',
            'created_at' => '2021-07-23',
        ]);

        DB::table('medicine_groups')->insert([
            'group_name' => 'Paket Operasi',
            'branch_id' => '1',
            'user_id' => '1',
            'created_at' => '2021-07-23',
        ]);

        DB::table('medicine_groups')->insert([
            'group_name' => 'Paket Steril',
            'branch_id' => '2',
            'user_id' => '1',
            'created_at' => '2021-07-23',
        ]);

        //price medicine group

        DB::table('price_medicine_groups')->insert([
            'medicine_group_id' => 3,
            'selling_price' => 500000.00,
            'capital_price' => 100000.00,
            'doctor_fee' => 100000.00,
            'petshop_fee' => 300000.00,
            'user_id' => 1,
            'created_at' => '2021-07-23',
        ]);

        DB::table('price_medicine_groups')->insert([
            'medicine_group_id' => 6,
            'selling_price' => 1000000.00,
            'capital_price' => 400000.00,
            'doctor_fee' => 400000.00,
            'petshop_fee' => 200000.00,
            'user_id' => 1,
            'created_at' => '2021-07-23',
        ]);

        DB::table('price_medicine_groups')->insert([
            'medicine_group_id' => 4,
            'selling_price' => 200000.00,
            'capital_price' => 50000.00,
            'doctor_fee' => 50000.00,
            'petshop_fee' => 100000.00,
            'user_id' => 1,
            'created_at' => '2021-07-23',
        ]);

        DB::table('price_medicine_groups')->insert([
            'medicine_group_id' => 13,
            'selling_price' => 1000000.00,
            'capital_price' => 200000.00,
            'doctor_fee' => 400000.00,
            'petshop_fee' => 400000.00,
            'user_id' => 1,
            'created_at' => '2021-07-23',
        ]);

        DB::table('price_medicine_groups')->insert([
            'medicine_group_id' => 11,
            'selling_price' => 150000.00,
            'capital_price' => 25000.00,
            'doctor_fee' => 50000.00,
            'petshop_fee' => 75000.00,
            'user_id' => 1,
            'created_at' => '2021-07-23',
        ]);

        DB::table('price_medicine_groups')->insert([
            'medicine_group_id' => 14,
            'selling_price' => 1300000.00,
            'capital_price' => 200000.00,
            'doctor_fee' => 500000.00,
            'petshop_fee' => 600000.00,
            'user_id' => 1,
            'created_at' => '2021-07-23',
        ]);

        // DB::table('detail_service_patients')->insert([
        //     'check_up_result_id' => '1',
        //     'price_service_id' => '2',
        //     'quantity' => '1',
        //     'price_overall' => '25000',
        //     'status_paid_off' => '0',
        //     'user_id' => '1',
        //     'created_at' => '2020-12-30'
        // ]);

        // DB::table('detail_service_patients')->insert([
        //     'check_up_result_id' => '1',
        //     'price_service_id' => '3',
        //     'quantity' => '1',
        //     'price_overall' => '25000',
        //     'status_paid_off' => '0',
        //     'user_id' => '1',
        //     'created_at' => '2020-12-30'
        // ]);

        DB::table('in_patients')->insert([
            'check_up_result_id' => '1',
            'description' => 'ini adalah contoh kondisi pasien',
            'user_id' => '1',
            'created_at' => '2020-12-30',
        ]);

        DB::table('in_patients')->insert([
          'check_up_result_id' => '1',
          'description' => 'ini adalah contoh kondisi pasien',
          'user_id' => '1',
          'created_at' => '2020-12-30',
      ]);

      DB::table('payrolls')->insert([
        'user_employee_id' => '1',
        'date_payed' => '2021-08-29',
        'basic_sallary' => 2000000.00,
        'accomodation' => 2000000.00,
        'percentage_turnover' => 20,
        'amount_turnover' => 120000.00,
        'total_turnover' => 40000.00,
        'amount_inpatient' => 50000.00,
        'count_inpatient' => 5,
        'total_inpatient' => 250000.00,
        'percentage_surgery' => 15,
        'amount_surgery' => 30000.00,
        'total_overall' => 30000.00,
        'total_surgery' => 30000.00,
        'total_overall' => 2650000,
        'created_at' => '2021-08-29'
    ]);

    DB::table('payrolls')->insert([
      'user_employee_id' => '2',
      'date_payed' => '2021-08-29',
      'basic_sallary' => 2000000.00,
      'accomodation' => 2000000.00,
      'percentage_turnover' => 20,
      'amount_turnover' => 120000.00,
      'total_turnover' => 40000.00,
      'amount_inpatient' => 50000.00,
      'count_inpatient' => 5,
      'total_inpatient' => 250000.00,
      'percentage_surgery' => 15,
      'amount_surgery' => 30000.00,
      'total_overall' => 30000.00,
      'total_surgery' => 30000.00,
      'total_overall' => 2650000,
      'created_at' => '2021-08-29'
  ]);

  DB::table('payrolls')->insert([
    'user_employee_id' => '3',
    'date_payed' => '2021-08-29',
    'basic_sallary' => 2000000.00,
    'accomodation' => 2000000.00,
    'percentage_turnover' => 20,
    'amount_turnover' => 120000.00,
    'total_turnover' => 40000.00,
    'amount_inpatient' => 50000.00,
    'count_inpatient' => 5,
    'total_inpatient' => 250000.00,
    'percentage_surgery' => 15,
    'amount_surgery' => 30000.00,
    'total_overall' => 30000.00,
    'total_surgery' => 30000.00,
    'total_overall' => 2650000,
    'created_at' => '2021-08-29'
]);
        // DB::table('list_of_payments')->insert([
        //     'check_up_result_id' => '1',
        //     'user_id' => '1',
        //     'created_at' => '2021-04-04',
        // ]);

        // DB::table('list_of_payments')->insert([
        //     'check_up_result_id' => '3',
        //     'user_id' => '1',
        //     'created_at' => '2021-04-11',
        // ]);

        // DB::table('list_of_payment_items')->insert([
        //     'check_up_result_id' => '1',
        //     'detail_item_patient_id' => '1',
        //     'user_id' => '1',
        //     'created_at' => '2020-04-04',
        // ]);

        // DB::table('list_of_payment_items')->insert([
        //     'check_up_result_id' => '1',
        //     'detail_item_patient_id' => '2',
        //     'user_id' => '1',
        //     'created_at' => '2020-04-04',
        // ]);

        // DB::table('list_of_payment_items')->insert([
        //     'check_up_result_id' => '1',
        //     'detail_item_patient_id' => '3',
        //     'user_id' => '1',
        //     'created_at' => '2020-04-04',
        // ]);

        // DB::table('list_of_payment_items')->insert([
        //     'check_up_result_id' => '3',
        //     'detail_item_patient_id' => '4',
        //     'user_id' => '1',
        //     'created_at' => '2020-04-04',
        // ]);

        // DB::table('list_of_payment_services')->insert([
        //     'check_up_result_id' => '1',
        //     'detail_service_patient_id' => '1',
        //     'user_id' => '1',
        //     'created_at' => '2020-04-04',
        // ]);

        // DB::table('list_of_payment_services')->insert([
        //     'check_up_result_id' => '3',
        //     'detail_service_patient_id' => '3',
        //     'user_id' => '1',
        //     'created_at' => '2020-04-04',
        // ]);

        // //history item hovement
        // DB::table('history_item_movements')->insert([
        //     'item_id' => '1',
        //     'quantity' => '1',
        //     'status' => 'kurang',
        //     'user_id' => '1',
        //     'created_at' => '2020-12-30'
        // ]);

        // DB::table('history_item_movements')->insert([
        //     'item_id' => '2',
        //     'quantity' => '1',
        //     'status' => 'kurang',
        //     'user_id' => '1',
        //     'created_at' => '2020-12-30'
        // ]);

        // DB::table('history_item_movements')->insert([
        //     'item_id' => '3',
        //     'quantity' => '1',
        //     'status' => 'kurang',
        //     'user_id' => '1',
        //     'created_at' => '2020-12-30'
        // ]);

        // DB::table('history_item_movements')->insert([
        //     'item_id' => '4',
        //     'quantity' => '1',
        //     'status' => 'kurang',
        //     'user_id' => '1',
        //     'created_at' => '2020-12-30'
        // ]);

        // DB::table('history_item_movements')->insert([
        //     'item_id' => '5',
        //     'quantity' => '1',
        //     'status' => 'kurang',
        //     'user_id' => '1',
        //     'created_at' => '2020-12-30'
        // ]);

        // DB::table('history_item_movements')->insert([
        //     'item_id' => '6',
        //     'quantity' => '1',
        //     'status' => 'kurang',
        //     'user_id' => '1',
        //     'created_at' => '2020-12-30'
        // ]);
    }
}
