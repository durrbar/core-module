<?php

declare(strict_types=1);

namespace Modules\Core\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\Core\Helpers\FileHelper as HelpersFileHelper;

/**
 * @method static \Modules\Core\Helpers\FileHelper setFile(\Illuminate\Http\UploadedFile $file)
 * @method static \Modules\Core\Helpers\FileHelper setPath(string $path)
 * @method static \Modules\Core\Helpers\FileHelper setDisk(string $disk)
 * @method static \Modules\Core\Helpers\FileHelper setVisibility(string $visibility)
 * @method static \Modules\Core\Helpers\FileHelper setHeight(int $height)
 * @method static \Modules\Core\Helpers\FileHelper setQuality(int $quality)
 * @method static \Modules\Core\Helpers\FileHelper generateUniqueFileName()
 * @method static \Modules\Core\Helpers\FileHelper upload()
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
