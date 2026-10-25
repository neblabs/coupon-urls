<?php declare(strict_types=1);

use CouponURLs\Original\Events\Wordpress\Framework\AppSubscribers;

it('file is readable!', function() {
   (object) $appSubscribers = new AppSubscribers;

   expect($appSubscribers->source())->toBeReadableFile();
});