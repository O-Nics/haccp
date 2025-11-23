<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\RestaurantType;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = [
            [
                'name' => 'Le Gourmet Parisien',
                'siret' => '12345678901234',
                'address' => '15 Rue de la Gastronomie',
                'postal_code' => '75001',
                'city' => 'Paris',
                'country' => 'FR',
                'phone' => '0142345678',
                'email' => 'contact@gourmet-parisien.fr',
                'type' => 'traditional',
            ],
            [
                'name' => 'Brasserie du Marché',
                'siret' => '98765432109876',
                'address' => '28 Avenue des Champs',
                'postal_code' => '69002',
                'city' => 'Lyon',
                'country' => 'FR',
                'phone' => '0478123456',
                'email' => 'info@brasserie-marche.fr',
                'type' => 'brasserie',
            ],
            [
                'name' => 'Quick Burger',
                'siret' => '55566677788899',
                'address' => '42 Boulevard de la Restauration',
                'postal_code' => '13001',
                'city' => 'Marseille',
                'country' => 'FR',
                'phone' => '0491234567',
                'email' => 'contact@quickburger.fr',
                'type' => 'fast_food',
            ],
        ];

        foreach ($restaurants as $restaurantData) {
            $type = RestaurantType::where('name', $restaurantData['type'])->first();

            if (! $type) {
                continue;
            }

            $restaurant = new Restaurant;
            $restaurant->restaurant_type_id = $type->id;
            $restaurant->name = $restaurantData['name'];
            $restaurant->siret = $restaurantData['siret'];
            $restaurant->address = $restaurantData['address'];
            $restaurant->postal_code = $restaurantData['postal_code'];
            $restaurant->city = $restaurantData['city'];
            $restaurant->country = $restaurantData['country'];
            $restaurant->phone = $restaurantData['phone'];
            $restaurant->email = $restaurantData['email'];
            $restaurant->save();
        }

        // Create 2 additional random restaurants
        for ($i = 0; $i < 2; $i++) {
            Restaurant::factory()->withRestaurantType()->create();
        }
    }
}
