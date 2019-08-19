<?php


namespace Violeta\CronModule\Output;


use Violeta\CronModule\Helper\ConfigHelper;
use Violeta\CronModule\Writer\WriterFactory;

class ChangesOutputWriter
{
    /** @var WriterFactory */
    private $writerFactory;
    /** @var ConfigHelper */
    private $helper;

    /**
     * ChangesOutputWriter constructor.
     * @param WriterFactory $writerFactory
     * @param ConfigHelper $helper
     */
    public function __construct(WriterFactory $writerFactory, ConfigHelper $helper)
    {
        $this->writerFactory = $writerFactory;
        $this->helper = $helper;
    }

    public function writeChanges(array $changes): string
    {
        $writer = $this->writerFactory->create($this->helper->getConfigValue('output_format'));
        $outputFile = $this->getOutputDir() . '/' . date('Ymd_His') . '.' . $writer->getExtension();
        $writer->write($outputFile, $changes);

        return $outputFile;
    }

    private function getOutputDir(): string
    {
        $outputDir = BP . '/' . $this->helper->getConfigValue('output_dir');
        if (!file_exists($outputDir)) {
            mkdir($outputDir);
        }
        return $outputDir;
    }
}
