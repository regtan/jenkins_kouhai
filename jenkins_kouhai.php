<?php
include_once('./SmartIRC.php');

class KouhaiBot
{

    public $jenkins_home = 'your_jenkins_home';

    function response(&$irc,&$data){
        $message = $data->message;
        $sentence = '';
        if (preg_match('/.*肉.*/',$message)){
            $sentence = '${name}先輩本とさやでおごってください';
        }else if(preg_match('/.*金.*/',$message)){
            $sentence = '${name}先輩・・・お金ないんですか？後輩にたかるんですか？';
        }else if(preg_match('/.*(かわいい|カワイイ).*/',$message)){
            $sentence = '${name}先輩　ありがとう！！';
        }else if(preg_match('/.*おっぱい.*/',$message)){
            $sentence = '${name}先輩　通報しますよ？';
            if (mt_rand() % 100 == 0) {
                $sentence = '${name}先輩なら…触ってもいいですよ';
            }else if(mt_rand() % 5 == 0){
                $sentence = 'release_senpai: ${name}先輩がしつこいです！！';
            }
        }else if(preg_match('/.*(眠|ねむ)い.*/',$message)){
            $sentence = '${name}先輩　冷蔵庫にredbull入ってますよ？';
        }else if(preg_match('/.*帰る.*/',$message)){
            $sentence = '${name}先輩　お疲れさまでした。退勤処理してくださいね。あとアメッシュ確認して帰った方がいいですよ？http://tokyo-ame.jwa.or.jp/';
        }else if(preg_match('/.*見抜き.*/',$message)){
            $sentence = '${name}先輩・・・しょうがないにゃあ';
        }else if(preg_match('/.*(ばか|バカ).*/',$message)){
            $sentence = '${name}先輩　ばかでごめんなさい・・・';
        }else if(preg_match('/.*もっとよく知りたい.*/',$message)){
            $sentence = '肉 金 おっぱい (眠/ねむ)い 帰る 見抜き ばか {project_name}ビルド';
        }
        $sentence = str_replace('${name}',$data->nick,$sentence);
        $irc->message(SMARTIRC_TYPE_NOTICE,$data->channel,$sentence);
    }

    function nice_merge(&$irc,&$data){
        $sentence = '${name}先輩 nice merge! ^^)b';
        $sentence = str_replace('${name}',$data->nick,$sentence);
        $irc->message(SMARTIRC_TYPE_NOTICE,$data->channel,$sentence);
    }
    
    function jenkins_start(&$irc,&$data){
        $message = $data->message;
        $project_name = preg_replace('/jenkins_kouhai.*:[[:space:]]*([a-zA-Z0-9]*).*ビルド.*/','$1',$message);
        $irc->message(SMARTIRC_TYPE_NOTICE,$data->channel,$project_name.'のビルドですね。しょうがないにゃあ');
        $ch = curl_init($jenkins_home.'/job/'.$project_name.'/build?delay=0sec');
        curl_exec($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);
        if ($header['http_code'] === 404){
            $irc->message(SMARTIRC_TYPE_NOTICE,$data->channel,$project_name.'のビルドできないよー＞＜');
        }else{
            $irc->message(SMARTIRC_TYPE_NOTICE,$data->channel,$project_name.'のビルド うごかしました');
        }
    }

}

DEFINE('BOT_NAME','jenkins_kouhai');
DEFINE('REAL_NAME','your_address@your_domain');

$bot = &new KouhaiBot();
$irc = &new Net_SmartIRC();
//$irc->setDebug(SMARTIRC_DEBUG_ALL);
$irc->setUseSockets(TRUE);
$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, 'jenkins_kouhai.*:.*ビルド.*', $bot, 'jenkins_start');
$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, 'jenkins_kouhai.*:.*', $bot, 'response');
$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '.*(merge|マージ).*', $bot, 'nice_merge');
$irc->connect('10.0.3.42', 6667);
$irc->login(BOT_NAME, REAL_NAME, 0, 'Net_SmartIRC');
$irc->join(array('#test'));
$irc->listen();
$irc->disconnect();
?>
