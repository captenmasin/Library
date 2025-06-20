<?php

if (! function_exists('getDominantColour')) {
    function getDominantColour($imagePath): string
    {
        $imageType = exif_imagetype($imagePath);

        // Create an image resource from the file based on its type
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $img = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $img = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $img = imagecreatefromgif($imagePath);
                break;
            default:
                throw new Exception('Unsupported image type.');
        }

        $width = imagesx($img);
        $height = imagesy($img);
        $rTotal = $gTotal = $bTotal = $total = 0;

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($img, $x, $y);
                $rTotal += ($rgb >> 16) & 0xFF;
                $gTotal += ($rgb >> 8) & 0xFF;
                $bTotal += $rgb & 0xFF;
                $total++;
            }
        }

        imagedestroy($img); // Free up memory

        $rAverage = round($rTotal / $total);
        $gAverage = round($gTotal / $total);
        $bAverage = round($bTotal / $total);

        return sprintf('#%02x%02x%02x', $rAverage, $gAverage, $bAverage);
    }
}

if (! function_exists('hexToRgb')) {
    function hexToRgb($hex): string
    {
        sscanf($hex, '#%02x%02x%02x', $r, $g, $b);

        return "$r, $g, $b";
    }
}
