<?php

namespace Egment\Traits;

use Egment\Image;

trait Basic
{

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getIm()
    {
        return $this->im;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getRealPath()
    {
        return $this->realPath;
    }

    public function setAlpha(int $value)
    {
        $this->alpha = $value;
        return $this;
    }

    public function setDefaultImageType(string $type)
    {
        $this->defaultImageType = $type;
    }

    public function getDefaultImageType()
    {
        return $this->defaultImageType;
    }

    public function __clone()
    {
        return new Image($this->path);
    }

    protected function makeIm($path = '')
    {
        $path = $path ?: $this->path;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        try {
            switch ($type) {
                case 'gif':
                    $this->im = imageCreateFromGif($path);
                    $this->mime = 'image/gif';
                    break;
                case 'jpg':
                case 'jpeg':
                case 'jpe':
                    $this->im = imageCreateFromJpeg($path);
                    $this->mime = 'image/jpeg';
                    break;
                case 'png':
                    $this->im = imageCreateFromPng($path);
                    $this->mime = 'image/png';
                    break;
                case 'bmp':
                case 'wbmp':
                    $this->im = imageCreateFromBmp($path);
                    $this->mime = 'image/bmp';
                    break;
            }
        } catch (\Exception $e) {
            return false;
        }
        return $this->im;
    }
}
