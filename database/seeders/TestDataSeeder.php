<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use App\Models\OrganizationPhone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Здания
        Building::factory(10)->create();

        // Деятельности: дерево
        $food = Activity::create(['name' => 'Еда']);
        $meat = Activity::create(['name' => 'Мясная продукция', 'parent_id' => $food->id]);
        $milk = Activity::create(['name' => 'Молочная продукция', 'parent_id' => $food->id]);

        $auto = Activity::create(['name' => 'Автомобили']);
        $trucks = Activity::create(['name' => 'Грузовые', 'parent_id' => $auto->id]);
        $cars = Activity::create(['name' => 'Легковые', 'parent_id' => $auto->id]);
        $parts = Activity::create(['name' => 'Запчасти', 'parent_id' => $cars->id]);
        $accessories = Activity::create(['name' => 'Аксессуары', 'parent_id' => $cars->id]);

        // Организации
        Organization::factory(20)->create()->each(function ($org) use ($meat, $milk, $parts, $accessories) {
            // Телефоны (от 1 до 3)
            OrganizationPhone::factory(rand(1, 3))->create([
                'organization_id' => $org->id,
            ]);

            // Случайные деятельности
            $org->activities()->attach(
                collect([$meat, $milk, $parts, $accessories])
                    ->pluck('id')
                    ->random(rand(1, 2))
                    ->toArray()
            );
        });
    }
}
