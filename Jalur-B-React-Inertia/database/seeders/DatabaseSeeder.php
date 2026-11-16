<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Pustaka\Models\Book;
use Modules\Pustaka\Models\Category;
class DatabaseSeeder extends Seeder {
 public function run():void {
  User::updateOrCreate(['email'=>'admin@sipustaka.test'],['name'=>'Admin Demo','password'=>Hash::make('password'),'role'=>'admin']);
  User::updateOrCreate(['email'=>'mahasiswa@sipustaka.test'],['name'=>'Mahasiswa Demo','password'=>Hash::make('password'),'role'=>'mahasiswa']);
  foreach([['Pemrograman','Buku teknologi dan pemrograman'],['Umum','Bacaan umum']] as [$name,$description]){
   $category=Category::firstOrCreate(['name'=>$name],['description'=>$description]);
   Book::firstOrCreate(['title'=>$name==='Pemrograman'?'Laravel untuk Pemula':'Belajar Efektif'],['category_id'=>$category->id,'author'=>'Tim SIPustaka','isbn'=>$name==='Pemrograman'?'LAT-001':'LAT-002','stock'=>5]);
  }
 }
}
