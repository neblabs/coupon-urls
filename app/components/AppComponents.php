<?php

namespace CouponURLs\App\Components;

use CouponURLs\App\Components\Abilities\ComponentsRegistrator;
use CouponURLs\App\Components\Abilities\MultipleComponentsProvider;
use CouponURLs\Original\Collections\Collection;

class AppComponents
{
    public function __construct(
        protected Collection $componentRegistrators = new Collection([])
    ) {}

    public function addRegistrator(ComponentsRegistrator $componentsRegistrator) : void
    {
        $this->componentRegistrators->push($componentsRegistrator);
    }

    public function registrator(string $id) : ComponentsRegistrator
    {
        return $this->componentRegistrators->find(
            fn(ComponentsRegistrator $componentsRegistrator) => $componentsRegistrator->id()
                                                                                      ->__is($id)
        );
    }

    public function register(MultipleComponentsProvider $multipleComponentsProvider) : void
    {
        (object) $compatibleComponentRegistrators = $this->componentRegistrators->getThoseThat(
            canRegisterUsing: $multipleComponentsProvider
        );

        $compatibleComponentRegistrators->perform(registerUsing: $multipleComponentsProvider);
    }
}

