<?php

namespace App\Services;

use Milon\Barcode\DNS1D;

class BarcodeService
{
    public static function generateEquipmentBarcode($equipmentId, $itemId = null)
    {
        // Generate a unique barcode format: EQ-[equipment_id]-[item_id]-[random]
        if ($itemId) {
            $barcodeValue = "EQ-{$equipmentId}-{$itemId}-" . substr(md5($equipmentId . $itemId . time()), 0, 6);
        } else {
            $barcodeValue = "EQ-{$equipmentId}-" . substr(md5($equipmentId . time()), 0, 8);
        }
        
        // Remove any special characters and ensure it's scannable
        $barcodeValue = preg_replace('/[^A-Z0-9\-]/', '', $barcodeValue);
        
        return $barcodeValue;
    }

    public static function generateBarcodeImage($barcodeValue)
    {
        $dns1d = new DNS1D();
        return $dns1d->getBarcodePNG($barcodeValue, 'C128', 2, 80, [0, 0, 0], true);
    }

    public static function validateBarcodeFormat($barcode)
    {
        // Validate the barcode format matches our system
        return preg_match('/^EQ-\d+-\d+-[A-Z0-9]+$/', $barcode) || 
               preg_match('/^EQ-\d+-[A-Z0-9]+$/', $barcode);
    }
}