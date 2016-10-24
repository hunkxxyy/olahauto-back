<?php

namespace App\utils;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;

use App\utils\Dictionary;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Illuminate\Support\Facades\DB;

class LogWrapper extends Model
{
    private $logLevelSwitch = [
        'debug' => true,
        'info' => true,
        'warning' => true,
        'error' => true];// you can turn off/on the loglevels to log.
    private $className;
    private $userId = 1;
    private $microservice;
    private $logLevelMethod;
    private $logger; // the monolog library
    /*Dictionary*/
    private $langs = ['http','en', 'hu', 'es'];
    private $message; //the current passed message index. This is an index field in dictionary table
    private $related;
    private $messageId; //messages id in dictionary table
    private $dictionary; //mesage Engis
    public $dictionary_local; //message current language
    private $logRoot='../storage/app/';

    function __construct($className)
    {
		  
        $this->logger = new Logger('logger');
        $this->logger->pushHandler(new StreamHandler($this->logRoot.'logs/billcity.log', Logger::DEBUG));
        //  $start = microtime(true);
        $this->dictionary = json_decode(file_get_contents($this->logRoot.'log.dictionary.hu'), TRUE);
        $this->dictionary_local = json_decode(file_get_contents($this->logRoot.'log.dictionary.hu'), TRUE);
        $this->messageId = json_decode(file_get_contents($this->logRoot.'log.dictionary.http'), TRUE);
        /*$time_elapsed_secs = microtime(true) - $start;
        print 'idő:'.$time_elapsed_secs;*/

    }

    public function response($response=[],$showRelated=false, $additionalElements=[['name'=>'', 'value'=>'']]   )
    {
      //  file_put_contents('hunk3.log', 'msg:'.$this->message);

        if ($this->message){
            $related=($showRelated)?$this->related:'';
            $response=[
                'message'=>[
                    'http_status'=>$this->messageId[$this->message],
                    'message'=>$this->dictionary[$this->message].' '.$related,
                    'message_in_current_language'=>$this->dictionary_local[$this->message],
                ],

                'return'=>$response
            ];
            foreach ($additionalElements as $element) {
                if ($element['name'])
                $response[$element['name']]=$element['value'];
            }


        }
        return $response;
    }

    public function error($method, $msg, $relatedObject = '', $exception = '')
    {
        $this->logLevelMethod = 'error';
        $this->createLog($method, $msg, $relatedObject, $exception);

    }

    public function warning($method, $msg, $relatedObject = '')
    {
        $this->logLevelMethod = 'warning';
        $this->createLog($method, $msg, $relatedObject);

    }

    public function info($method, $msg, $relatedObject = '')
    {
        $this->logLevelMethod = 'info';
        $this->createLog($method, $msg, $relatedObject);

    }

    public function debug($method, $msg, $relatedObject = '')
    {
        $this->logLevelMethod = 'debug';
        $this->createLog($method, $msg, $relatedObject);

    }

    private function createLog($method, $msg, $relatedObject, $exception = '')
    {

        /*if the $msg['msg'] is valid than it will bi transalated*/
        $this->message=$msg;
        $this->related=$relatedObject;
      //  $msg = ($this->dictionary[$msg] == '') ? 'NO TRANSALATED MESSAGE FOR THIS:' . $msg : $this->dictionary[$msg];
       // $msg = '';

        $log = [

            'microservice' => $this->microservice,
            'className' => $this->className,
            'userId' => $this->userId,
            'method' => $method,
            'msg' => $msg

        ];
        if (isset($relatedObject))
            $log['related'] = $relatedObject;
        if ($exception) $log['exception'] = $exception;
        $this->convertToMonologLibrary($log);
    }

    private function convertToMonologLibrary($logArr)
    {

        $type = $this->logLevelMethod;
        if ($this->logLevelSwitch[$type])
            $this->logger->${"type"}($this->logArr['className'], $logArr);


    }

    /*Dictionary this function will belong dictionary microservice. */
    private function createDictionaryToJsonFile()
    {
        $dictionary = DB::connection('system')->table('dictionary')->get();
        foreach ($this->langs as $lang) {

            file_put_contents($this->logRoot.'log.dictionary.' . $lang, json_encode($this->saveDictionary($dictionary, $lang), TRUE));

        }
    }

    private function saveDictionary($results, $lang)
    {
        $dictArr = [];
        foreach ($results as $d) {
            $dictArr[$d->index] = $d->$lang;
        }
        return $dictArr;

    }

}
