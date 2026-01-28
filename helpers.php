<?php

use Illuminate\Support\Facades\Storage;


if (!function_exists('fetch_image')) {
    function fetch_image($user)
    {
        if ($user?->image) {
            return cloudStorageExist() &&
                in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi'])
                ? render_frontend_cloud_image_if_module_exists(
                    'profile/' . $user->image,
                    load_from: $user->load_from,
                )
                : asset('assets/uploads/profile/' . $user->image);
        }
        return asset('assets/static/img/author/author.jpg');
    }
}
