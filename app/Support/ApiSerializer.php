<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;

final class ApiSerializer
{
    /** @return array<string, mixed> */
    public static function category(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
        ];
    }

    /** @return array<string, mixed> */
    public static function item(Item $item, bool $includeCategory = true): array
    {
        $data = [
            'id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
            'price' => (float) $item->price,
            'category_id' => $item->category_id,
        ];

        if ($includeCategory && $item->category !== null) {
            $data['category'] = self::category($item->category);
        } else {
            $data['category'] = null;
        }

        return $data;
    }

    /** @return array<string, mixed> */
    public static function user(User $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
        ];
    }
}
