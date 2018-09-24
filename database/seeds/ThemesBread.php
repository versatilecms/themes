<?php

use Versatile\Core\Seeders\AbstractBreadSeeder;

class ThemesBread extends AbstractBreadSeeder
{
    public function menu()
    {
        return [
            [
                'role' => 'admin',
                'title' =>  'Themes',
                'icon_class' => 'versatile-paint-bucket',
                'order' => 20,
                'route' => 'versatile.theme.index',
            ]
        ];
    }

    public function permissions()
    {
        return [
            [
                'name' => 'browse_themes',
                'description' => null,
                'table_name' => 'admin',
                'roles' => ['admin']
            ]
        ];
    }
}
