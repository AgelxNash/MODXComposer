<?php namespace Modxcomposer\Output;
/**
 * Created by PhpStorm.
 * User: Agel_Nash
 * Date: 03.01.14
 * Time: 16:51
 */

class StreamOutput extends \Symfony\Component\Console\Output\StreamOutput{
    protected function doWrite($message, $newline)
    {
        $stream = parent::getStream();
        if (false === @fwrite($stream, $message.($newline ? "<br />" : ''))) {
            throw new \RuntimeException('Unable to write output.');
        }
        fflush($stream);
    }
}