<?php

namespace AppBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use FFMpeg\Format\Audio\Flac as Flac;

class UploadAudioFileConsumer implements ConsumerInterface
{
    protected $mq_save_files_path;
    protected $ffmpeg;

    public function __construct($ffmpeg, $mq_save_files_path)
    {
        $this->ffmpeg = $ffmpeg;
        $this->mq_save_files_path = $mq_save_files_path;
    }

    public function execute(AMQPMessage $msg)
    {
        $isUploadSuccess = $this->UploadAudioFile($msg);
        if (!$isUploadSuccess) {
            return false;
        }
    }

    public function UploadAudioFile(AMQPMessage $msg)
    {
        if($file=json_decode($msg->body, true)) {

            if (file_put_contents(
                $this->mq_save_files_path
                . $file['name']
                . '-'
                . $file['session_id']
                . '.'
                . $file['ext'],
                $file['data'], LOCK_EX
            )) {

//                var_dump($this->mq_save_files_path);exit;
//                $audio = $this->ffmpeg->open($this->mq_save_files_path . $file['name'] . '.' . $file['ext']);
//
//                $format = new Flac();
//
//                $format->on('progress', function ($file, $audio, $format, $percentage) {
//                    file_put_contents($this->mq_save_files_path . $file['name'] . $percentage . '.yml', $percentage);
//                });
//
//                $format
//                    -> setAudioChannels(2)
//                    -> setAudioKiloBitrate(256);
//
//                $audio->save($format, $this->mq_save_files_path .  $file['name'] . '.flac');

                return true;

            } else {
                return false;
//                throw new \Exception('<error>Error, cant save file ' . $file['name'] . ',' . $file['ext'] . '!</error>');
            }

        } else {

            return false;

        }
    }
}
