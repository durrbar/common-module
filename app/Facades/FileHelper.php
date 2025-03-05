<?php

namespace Modules\Common\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\Common\Helpers\FileHelper as HelpersFileHelper;

/**
 * @method static \Modules\Common\Helpers\FileHelper setFile(\Illuminate\Http\UploadedFile $file)
 * @method static \Modules\Common\Helpers\FileHelper setPath(string $path)
 * @method static \Modules\Common\Helpers\FileHelper setDisk(string $disk)
 * @method static \Modules\Common\Helpers\FileHelper setVisibility(string $visibility)
 * @method static \Modules\Common\Helpers\FileHelper setHeight(int $height)
 * @method static \Modules\Common\Helpers\FileHelper setQuality(int $quality)
 * @method static \Modules\Common\Helpers\FileHelper generateUniqueFileName()
 * @method static \Modules\Common\Helpers\FileHelper upload()
 * @method static string getFileName()
 * @method static string getPath()
 */
class FileHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return HelpersFileHelper::class;
    }
}
