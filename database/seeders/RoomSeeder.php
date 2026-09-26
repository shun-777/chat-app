<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Room::create(['name' => 'おしゃべり']);
        Room::create(['name' => 'メモ']);
        Room::create(['name' => 'れんらく']);

        $this->command->info('ルームを3つ入れました');
    }
}