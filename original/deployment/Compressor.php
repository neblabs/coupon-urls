<?php

namespace CouponURLs\Original\Deployment;

use CouponURLs\Original\Characters\StringManager;
use Symfony\Component\Finder\Finder;
use ZipArchive;

#[\AllowDynamicProperties]
Class Compressor
{
    protected $copyDirectoryName;

    public function __construct($copyDirectoryName, $finalBuildDirectory, $projectFolderName)
    {
        $this->copyDirectoryName = $copyDirectoryName;
        $this->finalBuildDirectory = $finalBuildDirectory;
        $this->projectFolderName = $projectFolderName;
        $this->finder = new Finder;
    }

    public function compress()
    {
        (object) $zip = new ZipArchive();

        (string) $zipFileName = "{$this->finalBuildDirectory}/{$this->projectFolderName}.zip";

        (bool) $openResult = $zip->open(
            $zipFileName,
            ZipArchive::CREATE
        );

        if ($openResult > 1) throw new \Exception("cannot compress {$this->copyDirectoryName}, err_code: {$zip->getStatusString()}");

        foreach ($this->finder->files()->in($this->copyDirectoryName) as $fileToCompress) {
            $zip->addFile(
                $source = $fileToCompress->getRealPath(),
                $target = static::getLowerCaseTargetFileName(
                    $fileToCompress->getRealPath(),
                    $fileToCompress->getRelativePathname()
                )
            );
        }

        print "\n\n\n";
        print "\nCompressed: {$zip->numFiles} files.";
        print "\nStatus: {$zip->getStatusString()}";
        print "\n{$zipFileName}\n";

        $zip->close();

        return $zipFileName;
    }

    public static function getLowerCaseTargetFileName($absoluteFilePath, $relativeFilePath)
    {
        (object) $file = StringManager::create($relativeFilePath);

        if (!$file->startsWith('vendor')) {
            if ($file->endsWith('.php')) {
                return $file->toLowerCase();
            }
        }

        return $relativeFilePath;
    }
}