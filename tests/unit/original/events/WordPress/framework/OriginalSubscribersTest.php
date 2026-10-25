<?php declare(strict_types=1);

use CouponURLs\Original\Events\Wordpress\Framework\OriginalSubscribers;

it('file is readable!', function() {
   (object) $originalSubscribers = new OriginalSubscribers;

   expect($originalSubscribers->source())->toBeReadableFile();
});