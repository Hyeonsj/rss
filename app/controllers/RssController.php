<?php

use Phalcon\Mvc\View;
use Phalcon\Paginator\Adapter\Model as Paginator;

require_once __DIR__."../../libs/rsslib/rsslib.php";

class RssController extends ControllerBase {

    public function initialize()
    {
        parent::initialize();

    }

    public function indexAction()
    {

        $return_rss =  RSS_Display("http://uiandwe.tistory.com/feed", 5);

        $this->view->setVar("result", $return_rss);
        return true;

//        if(mb_check_encoding($return_rss, 'UTF-8')){
//            echo iconv("UTF-8", "euc-kr", $return_rss);
//        }
//
//        exit();
    }

    public function uploadAction()
    {

        $uploaddir = '../public/files/';
        $filename = basename($_FILES['temp_file']['name']);
        $md5filename = $uploaddir . md5("rss_".$filename);
        $ext = array_pop(explode(".","$filename"));

        if($_FILES['temp_file']['error'] === UPLOAD_ERR_OK) {
            cp("pass");
            if(strtolower($ext) == "php") {
                cp("확장자 php파일은 업로드 하실수 없습니다.");
            }
            else if($_FILES['temp_file']['size'] <= 0){
                cp("파일 업로드에 실패하였습니다.");
            } else {
                if(!is_uploaded_file($_FILES['temp_file']['tmp_name'])) {
                    cp("HTTP로 전송된 파일이 아닙니다.");
                } else {
                    // move_uploaded_file은 임시 저장되어 있는 파일을 ./uploads 디렉토리로 이동합니다.
                    if (move_uploaded_file($_FILES['temp_file']['tmp_name'], $md5filename)) {
                        cp("성공적으로 업로드 되었습니다.");
                    } else {
                        cp("파일 업로드 실패입니다.");
                    }
//                    mysql_query("insert into $db values  ('','$filename')");
                    //파일 읽기

                    $handle = fopen($md5filename, "r");
                    $file = "";
                    if ($handle) {
                        while (($line = fgets($handle)) !== false) {
                            $file .= $line;
                        }

                        fclose($handle);
                    } else {
                        // error opening the file.
                    }

                    $this->opml_parser($file);
                    return $this->setJsonContent("success");
                }
            }
        } else {
            cp(file_errmsg($_FILES['temp_file']['error']));
            return $this->setJsonContent("failed");
        }

        cp($_FILES);

    }


    public function opml_parser($str){
        $xml = new SimpleXMLElement($str);
        $op = "<ul>";
        foreach ($xml->children() as $second_gen)
        {

            foreach ($second_gen->children() as $third_gen)
            {
                foreach ($third_gen->children() as $fourth_gen)
                {
                    $op.=  ' <li><span class=\'content-title\'>' . $fourth_gen['title'].'</span><ul>' ;

                    foreach ($fourth_gen->children() as $fifth_gen)
                    {
                        $op.=  ' <li data-rss-url=\''.$fifth_gen['xmlUrl'] .'\'>' . $fifth_gen['title'].'</li>' ;

                    }
                    $op .= "</ul></li>";
                }
            }
        }
        $op.="</ul>";
        cp($op);
        return $op;
    }

    public function countChild($node) {
        return count($node->children());
    }

    public function nest($node,&$op)
    {
        while (countChild($node)) {
            foreach ($node->children() as $i) {
                $op .= '<ul>';
                $op .= '<li>' . $i['text'] . '</li>';
                nest($i, $op);
                $op .= '</ul>';
            }
            break;
        }
    }
}
