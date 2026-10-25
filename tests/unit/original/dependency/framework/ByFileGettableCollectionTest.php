<?php declare(strict_types=1);

use CouponURLs\Original\Abilities\FileReader;
use CouponURLs\Original\Abilities\ReadableFile;
use CouponURLs\Original\Collections\ByFileGettableCollection;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Files\RequireFileReader;
use CouponURLs\Original\Files\RequireOnceFileReader;

it('gets the types from a source file', function() {
    /** @var \Mockery\MockInterface&ReadableFile */
    (object) $registeredDependenciesFile = Mockery::mock(ReadableFile::class);
    /** @var \Mockery\MockInterface&FileReader */
    (object) $fileReader = Mockery::mock(FileReader::class);

    (object) $arrayOfRegisteredTypes = [
        Dependency::class
    ];

    (object) $registeredTypes = new ByFileGettableCollection(
        $registeredDependenciesFile,
        fileReader: $fileReader
    );

    $fileReader->shouldReceive('read')->once()->with($registeredDependenciesFile)->andReturn($arrayOfRegisteredTypes);
    
    expect($registeredTypes->get()->asArray())->toBe($arrayOfRegisteredTypes);
});


it('works with a default file reader', function() {
    /** @var \Mockery\MockInterface&ReadableFile */
    (object) $registeredDependenciesFile = Mockery::mock(ReadableFile::class);

    (object) $registeredTypes = new ByFileGettableCollection(
        $registeredDependenciesFile,
    );

    expect($registeredTypes)->toEqual(new ByFileGettableCollection(
        $registeredDependenciesFile,
        fileReader: new RequireFileReader
    ));
});