<?php namespace Modxcomposer\Output;

class DisplayOutput extends StreamOutput{
    public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null, \Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter = null){
        parent::__construct(fopen('php://memory', 'w+'), $verbosity, $decorated, $formatter);
    }

    public function getContent(){
        $tmp = '';
        $stream = $this->getStream();
        rewind($stream);
        while (!feof($stream)) {
            $tmp .= fread($stream, 8192);
        }
        return $tmp;
    }
}