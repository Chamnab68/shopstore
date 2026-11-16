<?php

namespace Tests\Unit;

use App\Support\PublicImage;
use Tests\TestCase;

class PublicImageTest extends TestCase
{
    public function test_local_image_paths_use_the_uploads_directory(): void
    {
        $this->assertSame(
            asset('uploads/products/example.jpg'),
            PublicImage::url('products/example.jpg')
        );
    }

    public function test_external_image_urls_are_returned_unchanged(): void
    {
        $url = 'https://cdn.example.com/products/example.jpg';

        $this->assertSame($url, PublicImage::url($url));
    }
}
