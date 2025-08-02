<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomExpenseType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'usage_count',
    ];

    /**
     * Add or update a custom expense type
     */
    public static function addOrUpdateType($name, $nameAr = null)
    {
        $nameAr = $nameAr ?: $name; // Use English name as Arabic name if not provided
        
        $type = self::where('name', $name)->orWhere('name_ar', $nameAr)->first();
        
        if ($type) {
            // Update usage count
            $type->increment('usage_count');
            return $type;
        } else {
            // Create new type
            return self::create([
                'name' => $name,
                'name_ar' => $nameAr,
                'usage_count' => 1,
            ]);
        }
    }

    /**
     * Get all expense types (built-in + custom) for dropdown
     */
    public static function getAllExpenseTypes()
    {
        // Built-in expense types
        $builtInTypes = [
            ['id' => 'electricity', 'name' => 'كهرباء', 'is_custom' => false],
            ['id' => 'water', 'name' => 'مياه', 'is_custom' => false],
            ['id' => 'internet', 'name' => 'إنترنت', 'is_custom' => false],
            ['id' => 'phone', 'name' => 'هاتف', 'is_custom' => false],
            ['id' => 'maintenance', 'name' => 'صيانة', 'is_custom' => false],
            ['id' => 'cleaning', 'name' => 'تنظيف', 'is_custom' => false],
            ['id' => 'security', 'name' => 'أمن', 'is_custom' => false],
        ];

        // Get custom expense types, ordered by usage count (most used first)
        $customTypes = self::orderBy('usage_count', 'desc')
            ->get()
            ->map(function ($type) {
                return [
                    'id' => 'custom_' . $type->id,
                    'name' => $type->name_ar,
                    'is_custom' => true,
                    'custom_id' => $type->id,
                ];
            })
            ->toArray();

        // Combine built-in and custom types
        $allTypes = array_merge($builtInTypes, $customTypes);
        
        // Add "Other" option at the end
        $allTypes[] = ['id' => 'other', 'name' => 'أخرى', 'is_custom' => false];

        return $allTypes;
    }

    /**
     * Get custom expense type by ID
     */
    public static function getCustomTypeById($customId)
    {
        return self::find($customId);
    }
}
