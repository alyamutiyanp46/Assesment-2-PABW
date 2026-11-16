<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Modules\Complaint\Models\Complaint;
use Modules\Facility\Models\Facility;
class DatabaseSeeder extends Seeder {
 public function run():void {
  $admin=User::updateOrCreate(['email'=>'admin@campuscare.test'],['name'=>'Admin Kampus','password'=>Hash::make('password'),'role'=>'admin']);
  $user=User::updateOrCreate(['email'=>'user@campuscare.test'],['name'=>'Mahasiswa Demo','password'=>Hash::make('password'),'role'=>'user']);
  $facility=Facility::firstOrCreate(['name'=>'Ruang Kelas A-101'],['location'=>'Gedung A lantai 1','category'=>'Ruang kelas','description'=>'Ruang kuliah kapasitas 35 orang','condition'=>'baik']);
  Facility::firstOrCreate(['name'=>'Proyektor B-204'],['location'=>'Gedung B lantai 2','category'=>'Elektronik','description'=>'Proyektor ruang kelas','condition'=>'rusak_ringan']);
  Complaint::firstOrCreate(['title'=>'Lampu berkedip','user_id'=>$user->id],['facility_id'=>$facility->id,'description'=>'Lampu di sisi kanan kelas berkedip.','location'=>$facility->location,'status'=>'menunggu']);
 }
}
