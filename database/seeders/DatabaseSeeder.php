<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            AdminUserSeeder::class,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password'), 'email_verified_at' => now(), 'remember_token' => Str::random(10), 'is_admin' => true]
        );
        if (! $admin->hasAnyRole(['ceo', 'cto'])) {
            $admin->assignRole('ceo');
        }

        $this->ensurePlaceholderImage();

        $this->call(CategorySeeder::class);
    }

    private function ensurePlaceholderImage(): void
    {
        $dir = 'placeholders';
        $path = $dir . '/placeholder.jpg';
        $fullPath = storage_path('app/public/' . $path);

        if (file_exists($fullPath)) {
            return;
        }

        $dirPath = dirname($fullPath);
        if (! is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        if (function_exists('imagecreatetruecolor')) {
            $img = imagecreatetruecolor(400, 400);
            if ($img !== false) {
                $grey = imagecolorallocate($img, 240, 240, 240);
                imagefill($img, 0, 0, $grey);
                imagejpeg($img, $fullPath, 85);
                imagedestroy($img);
                return;
            }
        }

        $gif1x1 = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        if ($gif1x1 !== false) {
            file_put_contents($fullPath, $gif1x1);
        }
    }
}
