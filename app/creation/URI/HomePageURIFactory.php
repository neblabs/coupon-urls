<?php

namespace CouponURLs\App\Creation\Uri;

use CouponURLs\App\Creation\Uri\QueryParametersFromStringFactory;
use CouponURLs\App\Domain\URIs\HomePageURI;
use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Creation\Abilities\CanCreateEntity;
use CouponURLs\Original\Creation\Abilities\CanCreateEntityWithParameters;
use CouponURLs\Original\Creation\Abilities\CreatableEntitiesWithParameters;
use CouponURLs\Original\Creation\Entities\Abilities\OverloadableEntitiesFactory;
use CouponURLs\Original\Data\Query\Parameters;
use CouponURLs\Original\Domain\Entities;
use CouponURLs\Original\Domain\Entity;
use Symfony\Component\HttpFoundation\Request;
use function CouponURLs\Original\Utilities\Text\i;

class HomePageURIFactory implements CanCreateEntity, OverloadableEntitiesFactory
{
    public function __construct(
        protected QueryParametersFromStringFactory $queryParametersFactory
    ) {}

    /** @param Collection $data The parse_url() parts */    
    public function canCreateEntity(mixed $data): bool
    {
        (object) $currentPath = i($data->get('path'))->ensureRight('/');
        (object) $homePath = i('/');

        return $currentPath->__is($homePath);
    } 

    /** @param Collection $data The parse_url() parts */
    public function createEntity(mixed $data): Entity
    {
        return new HomePageURI(
            queryParameters: $this->queryParametersFactory->create(
                $data->get('query') ?? ''
            )
        );
    } 

    public function canCreateEntities(mixed $data): bool
    {
        return false;        
    } 
}