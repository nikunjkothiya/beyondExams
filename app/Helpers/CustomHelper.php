<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

function commonUploadImage($storage_path, $file_path)
{
    $imageName = time().'.'.$file_path->getClientOriginalExtension();
    $file_path->move(public_path($storage_path), $imageName);
    return $storage_path.''.$imageName;
}
