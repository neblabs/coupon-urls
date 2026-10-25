<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Renderers;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Renderers\Abilities\Renderable;
use CouponURLs\Original\Renderers\RendererComposite;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class RendererCompositeTest extends WP_UnitTestCase
{
    
    
        

    public function test_renderers_all_renderables()
    {
        $oneRenderer = $this->createMock(Renderable::class);
        $twoRenderer = $this->createMock(Renderable::class);
        $threeRenderer = $this->createMock(Renderable::class);
        $threeAndAHalfRenderer = $this->createMock(Renderable::class);
        $fourRenderer = $this->createMock(Renderable::class);
        $fiveRenderer = $this->createMock(Renderable::class);
        $sixRenderer = $this->createMock(Renderable::class);

        (object) $rendererComposite = new RendererComposite([
            $oneRenderer,
            [
                $twoRenderer,
                [
                    $threeRenderer,
                    [$threeAndAHalfRenderer]
                ]
            ],
            new RendererComposite([
                $fourRenderer,
                [
                    $fiveRenderer,
                ]
            ]),
            new RendererComposite([
                new RendererComposite([
                    $sixRenderer
                ])
            ])
        ]);

        $oneRenderer->expects($this->once())
                    ->method('render')
                    ->willReturn('one');

        $twoRenderer->expects($this->once())
                    ->method('render')
                    ->willReturn('two');

        $threeRenderer->expects($this->once())
                    ->method('render')
                    ->willReturn('three');

        $threeAndAHalfRenderer->expects($this->once())
                    ->method('render')
                    ->willReturn('threeAndAHalf');

        $fourRenderer->expects($this->once())
                    ->method('render')
                    ->willReturn('four');

        $fiveRenderer->expects($this->once())
                    ->method('render')
                    ->willReturn('five');

        $sixRenderer->expects($this->once())
                    ->method('render')
                    ->willReturn('six');
        $this->assertSame('onetwothreethreeAndAHalffourfivesix', $rendererComposite->render());    
    }
}