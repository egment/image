<?php

namespace Egment\Traits;

trait Adjustment
{

    protected $im;

    //拷贝图像
    public function copy($srcIm, array $destStart, array $srcStart, $width, $height)
    {
        $destIm = $this->im;
        return imagecopy($destIm, $srcIm, $destStart[0], $destStart[1], $srcStart[0], $srcStart[1], $width, $height);
    }

    //拷贝图像并快速重置大小
    public function copyResize($destIm, $srcIm, array $destStart, array $srcStart, $destWidth, $destHeight, $srcWidth, $srcHeight)
    {
        $destIm = $this->im;
        return imagecopyresized($destIm, $srcIm, $destStart[0], $destStart[1], $srcStart[0], $srcStart[1], $destWidth, $destHeight, $srcWidth, $srcHeight);
    }

    //拷贝图像并调整大小 【速度稍慢，质量稍高】
    public function copyResampled($destIm, $srcIm, array $destStart, array $srcStart, $destWidth, $destHeight, $srcWidth, $srcHeight)
    {
        $destIm = $this->im;
        return imagecopyresampled($destIm, $srcIm, $destStart[0], $destStart[1], $srcStart[0], $srcStart[1], $destWidth, $destHeight, $srcWidth, $srcHeight);
    }

}
