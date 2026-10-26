<?php

namespace CouponURLs\App\Creation\Uri;

use CouponURLs\App\Domain\Uris\QueryParameters;
use CouponURLs\Original\Characters\StringManager;

use function CouponURLs\Original\Utilities\Collection\_a;
use function CouponURLs\Original\Utilities\Collection\a;
use function CouponURLs\Original\Utilities\Text\i;

class QueryParametersFromStringFactory
{
    public function create(string $query): QueryParameters
    {
        return new QueryParameters(
            _a(i($query)->removeLeft('?')->explode('&'))->mapWithKeys(
                fn(StringManager $keyAndValue) => a(
                    key: (string) _a($keyAndValue->explode('='))->first()->toLowerCase(),
                    value: (string) $keyAndValue->contains('=')? _a($keyAndValue->explode('='))->last()->toLowerCase() : ''
                )
            )
        );
    } 
}