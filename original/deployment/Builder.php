<?php

namespace CouponURLs\Original\Deployment;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[\AllowDynamicProperties]
Class Builder
{
    protected $currentDirectory;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->settingsReader = new SettingsReader;
        $this->buildFileManager = new BuildFileManager($input->getOption('target'), $output);
        $this->currentDirectory = $this->buildFileManager->getCurrentDirectory();
    }

    public function build()
    {
        (string) $copyDirectoryName = $this->buildFileManager->createCopyDirectory();

        $this->buildFileManager->cloneDirectories($copyDirectoryName);

        if (!$this->input->getOption('compress')) {
            $path = $copyDirectoryName;
        } else {
            $path = $this->buildFileManager->compressCopy($copyDirectoryName, $this->input->getOption('silent'));
        }

        $this->runScripts();

        echo $path;
    }

    public function runScripts()
    {
        Script::handle($this->settingsReader->settings->scripts->afterCompression);
    }

}
