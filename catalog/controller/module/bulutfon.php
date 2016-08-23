<?php

class ControllerModuleBulutfon extends Controller
{

    public function index()
    {

        $action = $this->request->get["action"];
        $token = $this->request->get["token"];

        $securityKey = $this->config->get("bulutfon_secureKey");

        if ($securityKey != $token) {
            echo json_encode(array(
                'message' => 'no match secure key'
            ));
            die();
        }

        if (method_exists($this, "action_$action")) {
            $metod = "action_$action";
            $this->$metod();
        } else {
            throw new Exception("Hatalı bilgiler");
        }


        die();
    }

    private function action_cron()
    {
        $sysDir = realpath(DIR_APPLICATION . "/../system/bulutfon/");
        include($sysDir . "/autoload.php");


        $masterToken = $this->config->get("bulutfon_masterKey");
        $smsBaslik = $this->config->get("bulutfon_sms_baslik");
        $provider = new \Bulutfon\OAuth2\Client\Provider\Bulutfon(array(
            'verifySSL' => false
        ));

        $ac = new \League\OAuth2\Client\Token\AccessToken(array('access_token' => $masterToken));

        $cronCount = $this->config->get("ayar_sms_cronCount");
        if (!$cronCount) {
            $cronCount = 10;
        }

        $smsQs = $this->db->query("SELECT * FROM " . DB_PREFIX . "sms_queue sq left join " . DB_PREFIX . "sms_template st ON sq.template_id = st.id Where sq.status=1 order by st.id asc limit 0,10 ")->rows;


        foreach ($smsQs as $sms) {
            $arguments = json_decode($sms["arguments"], true);
            $content = $sms["content"];
            foreach ($arguments as $arKey => $arValue) {
                $content = str_replace("{$arKey}", $arValue, $content);
            }
            if (isset($sms["phone_number"]) && $sms["phone_number"]) {
                $m = array(
                    'title' => $smsBaslik,
                    'receivers' => '9' . $sms["phone_number"],
                    'content' => $content
                );

                $sonuc = (array)$provider->sendMessage($ac, $m);
                if (isset($sonuc["message"]) && $sonuc["message"] == "Messages created successfully") {
                    $this->db->query("update " . DB_PREFIX . "sms_queue set sms_content='$content',status=2");
                }
            }
        }
        die();
    }


    public function on_order_add($orderId)
    {

        if ($this->config->get("bulutfon_notify_onOrderComplete")) {

            $templateId = 1;

            $order = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id=" . $orderId)->rows;
            $orderProduct = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id=" . $orderId)->rows;

            $arguments = [
                'ad' => $order[0]['firstname'],
                'soyad' => $order[0]['lastname'],
                'fiyat' => $order[0]['total'],
                'siparisNumarasi' => $orderId
            ];

            $jsonArguments = json_encode($arguments);

            $this->addQueue($order[0]['telephone'], $templateId, $jsonArguments);
        }

    }

    public function on_order_history_add($orderId)
    {

        if ($this->config->get("bulutfon_notify_onOrderStatusChange")) {

            $templateId = 2;

            $order = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id=" . $orderId)->rows;
            $orderStatus = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id=" . $order[0]['order_status_id'])->rows;

            $arguments = [
                'siparisNumarasi' => $orderId,
                'siparisDurumu' => $orderStatus[0]['name']
            ];

            $jsonArguments = json_encode($arguments);

            $this->addQueue($order[0]['telephone'], $templateId, $jsonArguments);
        }

    }

    public function on_customer_add($customerId)
    {

        if ($this->config->get("bulutfon_notify_onNewUser")) {

            $templateId = 3;

            $customer = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id=" . $customerId)->rows;

            $arguments = [
                'ad' => $customer[0]['firstname'],
                'soyad' => $customer[0]['lastname'],
            ];

            $jsonArguments = json_encode($arguments);

            $this->addQueue($customer[0]['telephone'], $templateId, $jsonArguments);
        }
    }

    public function addQueue($phoneNumber, $templateId, $arguments)
    {

        $rows = $this->db->query("SELECT * FROM " . DB_PREFIX . "sms_queue WHERE date_added='" . date('Y-m-d H:i:s') . "' and template_id='" . $templateId . "' and phone_number='" . $phoneNumber . "'")->rows;
        if (!$rows) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "sms_queue (date_added, status, arguments, template_id, phone_number) VALUES ( '" . date('Y-m-d H:i:s') . "', '1','" . $arguments . "','" . $templateId . "','" . $phoneNumber . "')");
        }
    }

}
