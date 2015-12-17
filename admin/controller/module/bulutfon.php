<?php

class ControllerModuleBulutfon extends Controller{

    private $error = array();

    public function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" .DB_PREFIX. "sms_template` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `date_added` datetime,
          `status` tinyint(1) DEFAULT '1',
          `name` text(2) NOT NULL,
          `content` text(2) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" .DB_PREFIX. "sms_queue` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `date_added` datetime NOT NULL,
          `status` tinyint(1) DEFAULT '1',
          `sms_content` text(2),
          `template_id` int(11) NOT NULL,
          `phone_number` text(2) NOT NULL,
          `arguments` text(2) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;");

        $sql = $this->db->query("SELECT * FROM ". DB_PREFIX."sms_template WHERE name='Sipariş Durumu Güncelledi' OR name='Sipariş Oluşturuldu' OR name='Yeni Üye Kayıt Oldu'");

        if($sql->num_rows == 0){
            $this->addTemplate('Sipariş Oluşturuldu', '{ad}{soyad}{fiyat}{siparis_numarasi}', 0);
            $this->addTemplate('Sipariş Durumu Güncelledi', '{siparis_durumu}{siparis_numarasi}', 0);
            $this->addTemplate('Yeni Üye Kayıt Oldu', '{ad}{soyad}', 0);
        }

        $this->load->model('extension/event');
        $this->model_extension_event->addEvent('sms2', 'post.order.add', 'module/bulutfon/on_order_add');
        $this->model_extension_event->addEvent('sms3', 'post.order.history.add', 'module/bulutfon/on_order_history_add');
        $this->model_extension_event->addEvent('sms4', 'post.customer.add', 'module/bulutfon/on_customer_add');

    }

    public function uninstall(){
        $this->load->model('extension/event');
        $this->model_extension_event->deleteEvent('sms2');
        $this->model_extension_event->deleteEvent('sms3');
        $this->model_extension_event->deleteEvent('sms4');

        $this->db->query("DROP TABLE ".DB_PREFIX."sms_queue");
        $this->db->query("DROP TABLE ".DB_PREFIX."sms_template");
    }

    public function index(){

        $this->install();
        $this->document->setTitle('Bulutfon Opencart Eklentisi');
        $this->load->model('setting/setting');

        $data['heading_title'] = 'Bulutfon Opencart Eklentisi';
        $data['action'] = $this->url->link('module/bulutfon', 'token=' . $this->session->data['token'], 'SSL');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => 'Ana Sayfa',
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => 'Module',
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => 'Bulutfon Opencart Eklentisi',
            'href'      => $this->url->link('module/bulutfon', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['default_sms_templates'] = $this->getSmsTemplate(0);
        $data['customer_sms_templates'] = $this->getSmsTemplate(1);
        $data['all_sms_template'] = $this->getSmsTemplate();
        $data['sms_queue'] = $this->getSmsQueue();
        $data['customers'] = $this->getCustomer();

        $data['tab_general'] = 'deneme1';
        $data['tab_data'] = 'deneme1';
        $data['tab_design'] = 'deneme1';

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        if(isset($this->request->post['template_update'])){
            $smsName = $this->request->post['sms_name'];
            $smsContent = $this->request->post['sms_content'];
            if($smsName != '' && $smsContent != ''){
                $this->addTemplate($smsName, $smsContent, 1);
            }else {
                $templates = $this->request->post['templates'];
                foreach ($templates as $key => $template) {
                    $this->db->query("UPDATE " . DB_PREFIX . "sms_template SET content='" . $template . "' WHERE name='" . $key . "'");
                }
            }

            return $this->response->redirect($this->url->link('module/bulutfon', 'token=' . $this->session->data['token'], 'SSL'));
        }

        if(isset($this->request->post['customers'])){

            $smsId = $this->request->post['sms_id'];
            $customersIds = $this->request->post['customerId'];

            if($customersIds != ''){
                foreach($customersIds as $id){
                    $customer = $this->db->query("SELECT * FROM ". DB_PREFIX."customer WHERE customer_id=".$id)->rows;
                    $arguments = [
                        'ad' => $customer[0]['firstname'],
                        'soyad' => $customer[0]['lastname'],
                    ];
                    $jsonArguments = json_encode($arguments);
                    $this->addQueue($customer[0]['telephone'], $smsId, $jsonArguments);
                }
            }

            return $this->response->redirect($this->url->link('module/bulutfon', 'token=' . $this->session->data['token'], 'SSL'));
        }

        if(isset($this->request->post['setting_update'])){
            $postData = $this->request->post;
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('bulutfon', $postData);
            return $this->response->redirect($this->url->link('module/bulutfon', 'token=' . $this->session->data['token'], 'SSL'));
        }


        /* Ayarlar Populate */

        $data["ayar_masterKey"] = $this->config->get("bulutfon_masterKey");
        $data["ayar_notify_onOrderComplete"] = $this->config->get("bulutfon_notify_onOrderComplete");
        $data["ayar_notify_onOrderStatusChange"] = $this->config->get("bulutfon_notify_onOrderStatusChange");
        $data["ayar_notify_onNewUser"] = $this->config->get("bulutfon_notify_onNewUser");
        $data["ayar_sms_cronCount"] = $this->config->get("bulutfon_sms_cronCount");
        $data["ayar_sms_baslik"] = $this->config->get("bulutfon_sms_baslik");
        $data["ayar_sms_numaralar"] = $this->config->get("bulutfon_sms_numaralar");
        $data["ayar_sms_cronCount"] = !empty($data["ayar_sms_cronCount"]) ? $data["ayar_sms_cronCount"] : 10;


        $data["securityKey"] = $this->config->get("bulutfon_secureKey");
        if(!$data["securityKey"]){
            $data["securityKey"] = md5(uniqid().$_SERVER["HTTP_HOST"]);
        }

        $httpUrl = 'http://';
        if($this->config->get('config_secure')){
            $httpUrl = 'https://';
        }
        $url = "$httpUrl$_SERVER[HTTP_HOST]";
        $data["url"] = $url;


        /* Arama Kayıtları */
        $sysDir = realpath(DIR_APPLICATION."/../system/bulutfon/");
        include($sysDir."/autoload.php");

        $masterToken =  $data["ayar_masterKey"];
        $provider = new \Bulutfon\OAuth2\Client\Provider\Bulutfon(array(
            'verifySSL'=>false
        ));


        $data["cdrs"] = [];
        if($masterToken){
            $token = new \League\OAuth2\Client\Token\AccessToken(array('access_token'=>$masterToken));


            $referans["direction"]["IN"] = "<i class='fa fa-arrow-circle-o-right'></i> Gelen";
            $referans["direction"]["OUT"] = "Giden <i class='fa fa-arrow-circle-o-left'></i>";
            $referans["direction"]["LOCAL"] = "İç Hat <i class='fa fa-users'></i>";

            $referans["bf_calltype"]["voice"] = "<i class='fa fa-phone'></i> Sesli";
            $referans["bf_calltype"]["fax"] = "<i class='fa fa-print'></i> Fax";

            $cdrConfig = array();
            if($data["ayar_sms_numaralar"]){
                $cdrConfig = array('callee' => $data["ayar_sms_numaralar"]);
            }

            $cdrs = $provider->getCdrs($token, $cdrConfig )->getArrayCopy();
            if(isset($cdrs["cdrs"])){
                $cdrs = $cdrs["cdrs"];
                foreach($cdrs as $cdr){
                    /** @var $cdr \Bulutfon\OAuth2\Client\Entity\Cdr */;
                    $cdrData = $cdr->getArrayCopy();

                    $cdrData["direction_str"] = "";
                    $cdrData["bf_calltype_str"] = "";

                    $cdrData["caller_str"] = $this->formatPhoneNumber($cdrData["caller"]);
                    $cdrData["callee_str"] = $this->formatPhoneNumber($cdrData["callee"]);

                    if($callerStr= $this->getNumberName($cdrData["caller"])){
                        $cdrData["caller_str"] = ($callerStr);
                    }

                    if($calleeStr= $this->getNumberName($cdrData["callee"])){
                        $cdrData["callee_str"] = ($calleeStr);
                    }

                    if(isset($referans["direction"][$cdrData["direction"]])){
                        $cdrData["direction_str"] = $referans["direction"][$cdrData["direction"]];
                    }
                    if(isset($referans["bf_calltype"][$cdrData["bf_calltype"]])){
                        $cdrData["bf_calltype_str"] = $referans["bf_calltype"][$cdrData["bf_calltype"]];
                    }

                    $data["cdrs"][] = $cdrData;
                }
            }
        }

        $this->response->setOutput($this->load->view('module/bulutfon.tpl', $data));
    }

    public function getNumberName($number){
        if(strlen($number)==12){
            $number = substr($number,1,12);
        }

        $query = $this->db->query("Select firstname,lastname from " . DB_PREFIX . "customer where telephone='$number' limit 1");

        if($row = $query->row){
            return $row["firstname"]." ".$row["lastname"];
        }

        return "";
    }

    private function formatPhoneNumber($phone)
    {


        if(strlen($phone)==12){
            $phone = substr($phone,2,strlen($phone));
        }
        $phone = preg_replace("/[^0-9]/", "", $phone);



        if(strlen($phone) == 7)
            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
        elseif(strlen($phone) == 10)
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
        else
            return $phone;
    }

    public function getSmsTemplate($status = null){

        if($status == null){
            return $this->db->query("SELECT * FROM ". DB_PREFIX."sms_template")->rows;
        }
        return $this->db->query("SELECT * FROM ". DB_PREFIX."sms_template WHERE status='".$status."'")->rows;
    }

    public function getSmsQueue(){

        return $this->db->query("SELECT * FROM ". DB_PREFIX."sms_queue")->rows;
    }

    public function getCustomer(){

        return $this->db->query("SELECT * FROM ".DB_PREFIX."customer")->rows;
    }

    public function addTemplate($smsName, $smsContent, $status = 1){

        $this->db->query("INSERT INTO ".DB_PREFIX."sms_template (date_added, status, name, content) VALUES ( '".date('Y-m-d H:i:s')."', '".$status."','".$smsName."','".$smsContent."')");
    }
    public function addQueue($phoneNumber, $templateId, $arguments){

        $this->db->query("INSERT INTO ".DB_PREFIX."sms_queue (date_added, status, arguments, template_id, phone_number) VALUES ( '".date('Y-m-d H:i:s')."', '1','".$arguments."','".$templateId."','".$phoneNumber."')");
    }

    public function getToken(){
        $token = date('His');
        return $token;
    }

    public function searchPhone(){

    }

}