<?php

/**
 * Encode image resource to base64 code.
 *
 * @param [type] $image_file
 * @return String
 */
function imageToBase64($imagePath)
{
    if (!file_exists($imagePath)) {
        throw new \ErrorException("Image File not exists!");
    }
    $base64_image = '';
    $image_info = getimagesize($imagePath);
    $image_data = fread(fopen($imagePath, 'r'), filesize($imagePath));
    $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
    return $base64_image;
}
