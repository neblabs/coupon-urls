<?php declare(strict_types=1);

use CouponURLs\Original\Characters\StringManager;
use CouponURLs\Original\Data\Schema\Fields\Field;

beforeEach(function() {
   $this->field = new Field(name: 'meta_key', alias: 'key');
});

it('gets name', function() {
    expect($this->field->name())->toBeInstanceOf(StringManager::class)->toEqual('meta_key');
});

it('is true with real and alias names', function() {
    expect($this->field->is('meta_key'))->toBeTrue();
    expect($this->field->is('key'))->toBeTrue();
});

it('is false when giving it a non real and non aliased name', function() {
  expect($this->field->is('id'))->toBeFalse();
});