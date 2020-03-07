<?php

/**
 * Encode image resource to base64 code.
 *
 * @param [type] $image_file
 * @return String
 */
function imageToBase64($resource)
{
    if (is_string($resource) && file_exists($resource)) {
        $base64_image = '';
        $image_info = getimagesize($resource);
        $image_data = fread(fopen($resource, 'r'), filesize($resource));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . base64_encode($image_data);
        return $base64_image;
    }
    throw new ErrorException('不存在的资源路径或不支持的资源！');
}

function imToBase64($resource, $mime)
{
    return 'data:' . $mime . ';base64,' . base64_encode($resource);
}

/**
 * Make source picture with mask image.
 * Useage e.g.
 *   $source = imagecreatefrompng( $sourcePic );
 *   $mask = imagecreatefrompng( $maskPic);
 *   // Apply mask to source
 *   // imagealphamask( $source, $mask );
 *   $this->imagealphamask( $source, $mask );
 *   // Output
 *   header( "Content-type: image/png");
 *   imagepng( $source,"assets/images/crop/".$cropPicName.".png" );
 *   imagedestroy($source);
 * @param [type] $picture
 * @param [type] $mask
 * @return void
 */
function imagealphamask(&$picture, $mask)
{
    // Get sizes and set up new picture
    $xSize = imagesx($picture);
    $ySize = imagesy($picture);
    $newPicture = imagecreatetruecolor($xSize, $ySize);
    imagesavealpha($newPicture, true);
    imagefill($newPicture, 0, 0, imagecolorallocatealpha($newPicture, 100, 100, 0, 127));

    // Resize mask if necessary
    // if( $xSize != imagesx( $mask ) || $ySize != imagesy( $mask ) ) {
    //     $tempPic = imagecreatetruecolor( $xSize, $ySize );
    //     imagecopyresampled( $tempPic, $mask, 0, 0, 0, 0, $xSize, $ySize, imagesx( $mask ), imagesy( $mask ) );
    //     imagedestroy( $mask );
    //     $mask = $tempPic;
    // }

    // Perform pixel-based alpha map application
    for ($x = 0; $x < $xSize; $x++) {
        for ($y = 0; $y < $ySize; $y++) {
            $alpha = imagecolorsforindex($mask, imagecolorat($mask, $x, $y));
            //small mod to extract alpha, if using a black(transparent) and white
            //mask file instead change the following line back to Jules's original:
            // $alpha = 127 - floor($alpha['black'] / 2);
            //or a white(transparent) and black mask file:
            // $alpha = floor($alpha['black'] / 2);
            $alpha = $alpha['alpha'];
            $color = imagecolorsforindex($picture, imagecolorat($picture, $x, $y));
            //preserve alpha by comparing the two values
            if ($color['alpha'] > $alpha) {
                $alpha = $color['alpha'];
            }

            //kill data for fully transparent pixels
            if ($alpha == 127) {
                $color['red'] = 0;
                $color['blue'] = 0;
                $color['green'] = 0;
            }
            imagesetpixel($newPicture, $x, $y, imagecolorallocatealpha($newPicture, $color['red'], $color['green'], $color['blue'], $alpha));
        }
    }

    // Destory old picture and copy back to original picture
    imagedestroy($picture);
    $picture = $newPicture;
}

function trans_resource($resource, $mime)
{
    if (!is_resource($resource)) {
        return;
    }
    ob_start();
    resourceStream($resource, $mime);
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

function resourceStream($resource, $mime)
{
    if (($pos = strrpos($mime, '/')) !== false) {
        $ext = substr($mime, $pos + 1);
    }
    $ext = $mime;
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
        case 'jpe':
            imagejpeg($resource);
            break;
        case 'png':
            imagepng($resource);
            break;
        case 'gif':
            imagegif($resource);
            break;
        case 'bmp':
        case 'wbmp':
            imagebmp($resource);
            break;
        default:
            imagejpeg($resource);
    }
}

function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
{
    /* 下面两行只在线段直角相交时好使
    imagesetthickness($image, $thick);
    return imageline($image, $x1, $y1, $x2, $y2, $color);
     */
    if ($thick == 1) {
        return imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
    }
    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
    $a = $t / sqrt(1 + pow($k, 2));
    $points = array(
        round($x1 - (1 + $k) * $a), round($y1 + (1 - $k) * $a),
        round($x1 - (1 - $k) * $a), round($y1 - (1 + $k) * $a),
        round($x2 + (1 + $k) * $a), round($y2 - (1 - $k) * $a),
        round($x2 + (1 - $k) * $a), round($y2 + (1 + $k) * $a),
    );
    imagefilledpolygon($image, $points, 4, $color);
    return imagepolygon($image, $points, 4, $color);
}

function imagearcthick($image, $x, $y, $w, $h, $s, $e, $color, $thick = 1)
{
    if ($thick == 1) {
        return imagearc($image, $x, $y, $w, $h, $s, $e, $color);
    }
    for ($i = 1; $i < ($thick + 1); $i++) {
        imagearc($image, $x, $y, $w - ($i / 5), $h - ($i / 5), $s, $e, $color);
        imagearc($image, $x, $y, $w + ($i / 5), $h + ($i / 5), $s, $e, $color);
    }
}
