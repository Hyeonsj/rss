<?php

use Phalcon\Mvc\View;
use Phalcon\Paginator\Adapter\Model as Paginator;

require_once __DIR__."../../libs/rsslib/rsslib.php";
require_once __DIR__."../../libs/simple_html_dom/simple_html_dom.php";

use \Curl\Curl;

class RssController extends ControllerBase {

    public function initialize()
    {
        parent::initialize();

    }

    public function indexAction()
    {
        if($this->logged_user==null) {
            return $this->response->redirect("/user", true);
        }

        $user_menu = UserMenu::find("user_id='{$this->logged_user->user_id}'");
        $this->view->setVar("user_menu", $user_menu);

    }


    public function getContentAction(){

        $rss_url = $this->request->getPost("rss_url");
        $view_type = $this->request->getPost("view_type");
        cp("-----------------------");
        cp($rss_url);
        cp($view_type);
        cp("-----------------------");
        $user_menu = UserMenu::findFirst(array("conditions" => "user_id='{$this->logged_user->user_id}' and link='{$rss_url}'"));

        if($user_menu){
            if($view_type != ""){
                $user_menu->view_type = $view_type;
                $user_menu->save();
            }

            $user_menu->readed_at = time();
            $user_menu->save();

            if($user_menu->view_type == "" or $user_menu->view_type == "list"){
                cp("-----------------------");
                cp("list");
                cp("-----------------------");
                $return_rss =  RSS_Display($rss_url, 10, true, true);
            }
            else if($user_menu->view_type == "gally"){
                cp("-----------------------");
                cp("gally");
                cp("-----------------------");
                $return_rss =  RSS_Gally_Display($rss_url, 10, true, true);
            }
            else if($user_menu->view_type == "blog"){
                cp("-----------------------");
                cp("blog");
                cp("-----------------------");
                $return_rss =  RSS_blog_Display($rss_url, 10, true, true);
            }


        }

        $return_obj = new stdClass();
        $return_obj->view_type = $user_menu->view_type;
        $return_obj->html = $return_rss;
        $return_obj->image_list = $this->getRssImageList($rss_url);

        return $this->setJsonContent("success", "", $return_obj);
    }


    public function getRssImageList($rss_url){
        $rss_list = GET_RSS_link($rss_url, 10);
        $return_image_list = array();
        foreach($rss_list as $rss_url_obj){
            $sitecontent_list = SiteContent::findFirst("url='{$rss_url_obj}'");
            if($sitecontent_list){
                cp($sitecontent_list->image);
                if($sitecontent_list->image){
                    $return_image_list[] = $sitecontent_list->image;
                }
                else{
                    $return_image_list[] = "";
                }
            }
            else{
                $return_image_list[] = "";
            }
        }

        return $return_image_list;
    }


    public function getRssImageAction(){

        $proxies = array(); // Declaring an array to store the proxy list

        cp("=------------------------");
        $curl = curl_init();

        // setup headers - used the same headers from Firefox version 2.0.0.6
        // below was split up because php.net said the line was too long. :/
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: "; //browsers keep this blank.
        $url = "http://blog.daum.net/yeshira/2535";
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_REFERER, 'http://www.google.com');
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Connection: close'));
        $html = curl_exec($curl); //execute the curl command
        curl_close($curl);
        cp($html);
        cp(strpos($html, "이 페이지를 보려면, 프레임을 볼 수 있는 브라우저가 필요합니다"));
        cp("=------------------------");
    }



    public function curlTestAction(){

        cp("---------------------");
        cp("curlTestAction");
        cp("---------------------");

        $rss_list = Rss::find();
        foreach($rss_list as $rss_obj){
            cp($rss_obj->title);
            $return_rss =  RSS_link_list($rss_obj->link, 10, true);

            foreach($return_rss as $key=>$val){

                $site = Site::findFirst("url='{$val}'");
                if(!$site){
                    /** @var $val Site */
                    $site = new Site();
                    $site->url = $val;
                    if($site->save()){
                        $context = stream_context_create();
                        stream_context_set_params($context, array('user_agent' => 'UserAgent/1.0'));
                        $html = file_get_html($val, 0, $context);

                        /** @var $val SiteContent */
                        $sitecontent = new SiteContent();
                        $sitecontent->url = $val;
                        $sitecontent->site_id = $site->site_id;
                        $sitecontent->image = $html->find('img')[0]->src;
                        $sitecontent->content = htmlspecialchars($html);

                        $sitecontent->save();
                    }

                }


            }
        }




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
                    $return_data = htmlspecialchars($this->opml_parser($file));

                    return $this->setJsonContent("success", "", $return_data);
                }
            }
        } else {
            cp(file_errmsg($_FILES['temp_file']['error']));
            return $this->setJsonContent("failed");
        }

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
                    $op.=  " <li><span class='content-title'>" . $fourth_gen["title"]."</span><ul>" ;

                    foreach ($fourth_gen->children() as $fifth_gen)
                    {
                        $op.=  " <li data-rss-url='".$fifth_gen["xmlUrl"] ."'>" . $fifth_gen["title"]."</li>" ;
                        $usermenu = UserMenu::find(array("conditions"=>"user_id={$this->logged_user->user_id} and link='{$fifth_gen["xmlUrl"]}'"));
                        if(count($usermenu) == 0){
                            $this->db->begin();
                            try {
                                /** @var $val UserMenu */
                                $usermenu1 = new UserMenu();
                                $usermenu1->title .= $fifth_gen["title"];
                                $usermenu1->link .= $fifth_gen["xmlUrl"];
                                $usermenu1->user_id = $this->logged_user->user_id;

                                $usermenu1->save();
                                } catch(Exception $e) {
                                $this->db->rollback();
                                return $this->setJsonContent("error", $e->getMessage());
                            }

                            $rss = Rss::findFirst("link='{$fifth_gen["xmlUrl"]}'");
                            if(!$rss){
                                /** @var $val Rss */
                                $rss = new Rss();
                                $rss->title .= $fifth_gen["title"];
                                $rss->link .= $fifth_gen["xmlUrl"];
                                $rss->save();
                            }

                        }
                    }
                    $op .= "</ul></li>";
                }
            }
        }
        $op.="</ul>";

        return $op;
    }

    public function countChild($node) {
        return count($node->children());
    }

    public function nest($node,&$op)
    {
        while (countChild($node)) {
            foreach ($node->children() as $i) {
                $op .= "<ul>";
                $op .= "<li>" . $i["text"] . "</li>";
                nest($i, $op);
                $op .= "</ul>";
            }
            break;
        }
    }


}
