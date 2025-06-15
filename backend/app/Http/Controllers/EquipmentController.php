<?php

// app/Http/Controllers/EquipmentController.php
class EquipmentController extends Controller
{
    public function uploadImage(Request $request, $equipmentId)
    {
        // Validate the uploaded image
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Find the equipment
        $equipment = Equipment::findOrFail($equipmentId);

        // Handle file upload (store in storage/app/public/equipment-images)
        $imagePath = $request->file('image')->store('equipment-images', 'public');
        $imageUrl = Storage::url($imagePath);

        // **HERE'S YOUR LOGIC** - Determine image type
        $imageType = $equipment->images()->count() == 0 ? 1 : 2; // First = primary, rest = additional

        // Create the equipment image record
        $equipment->images()->create([
            'image_url' => $imageUrl,
            'type_id' => $imageType,
            'description' => $request->input('description'),
            'sort_order' => $equipment->images()->count() + 1
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'type' => $imageType == 1 ? 'primary' : 'additional'
        ]);
    }

    // Alternative: Bulk upload method
    public function uploadMultipleImages(Request $request, $equipmentId)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $equipment = Equipment::findOrFail($equipmentId);
        $currentImageCount = $equipment->images()->count();
        
        foreach ($request->file('images') as $index => $image) {
            $imagePath = $image->store('equipment-images', 'public');
            $imageUrl = Storage::url($imagePath);
            
            // First overall image = primary, rest = additional
            $imageType = ($currentImageCount + $index) == 0 ? 1 : 2;
            
            $equipment->images()->create([
                'image_url' => $imageUrl,
                'type_id' => $imageType,
                'sort_order' => $currentImageCount + $index + 1
            ]);
        }

        return response()->json(['message' => 'Images uploaded successfully']);
    }
}

// app/Models/Equipment.php
class Equipment extends Model
{
    public function images()
    {
        return $this->hasMany(EquipmentImage::class, 'equipment_id', 'equipment_id');
    }

    // Helper method to get primary image
    public function primaryImage()
    {
        return $this->images()->where('type_id', 1)->first();
    }

    // Helper method to get additional images
    public function additionalImages()
    {
        return $this->images()->where('type_id', 2)->orderBy('sort_order')->get();
    }
}

// app/Models/EquipmentImage.php
class EquipmentImage extends Model
{
    protected $fillable = ['equipment_id', 'image_url', 'description', 'sort_order', 'type_id'];
    
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'equipment_id');
    }
}