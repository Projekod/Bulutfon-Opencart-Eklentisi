<?php

class ControllerModuleBulutfon extends Controller {

    public function index() {

        $action = $this->request->get["action"];

        if(method_exists($this,"action_$action")){
            $metod = "action_$action";
            $this->$metod();
        }else{
            throw new Exception("Hatalı bilgiler");
        }


        die();
    }

    private function action_cron(){
        include (realpath(DIR_APPLICATION.'../bulutfonlibs')).'/autoload.php';


        die();
    }


    public function on_order_add($orderId) {

        $templateId = 1;

        $order = $this->db->query("SELECT * FROM ". DB_PREFIX."order WHERE order_id=".$orderId)->rows;
        $orderProduct = $this->db->query("SELECT * FROM ". DB_PREFIX."order_product WHERE order_id=".$orderId)->rows;

        $arguments = [
            'ad' => $order[0]['firstname'],
            'soyad' => $order[0]['lastname'],
            'fiyat' => $order[0]['total'],
            'siparisNumarasi' => $orderId
        ];

        $jsonArguments = json_encode($arguments);

        $this->addQueue($order[0]['telephone'], $templateId, $jsonArguments);

    }

    public function on_order_history_add($orderId){

        $templateId = 2;

        $order = $this->db->query("SELECT * FROM ". DB_PREFIX."order WHERE order_id=".$orderId)->rows;
        $orderStatus = $this->db->query("SELECT * FROM ". DB_PREFIX."order_status WHERE order_status_id=".$order[0]['order_status_id'])->rows;

        $arguments = [
            'siparisNumarasi' => $orderId,
            'siparisDurumu' => $orderStatus[0]['name']
        ];

        $jsonArguments = json_encode($arguments);

        $this->addQueue($order[0]['telephone'], $templateId, $jsonArguments);

    }

    public function on_customer_add($customerId){

        $templateId = 3;

        $customer = $this->db->query("SELECT * FROM ". DB_PREFIX."customer WHERE customer_id=".$customerId)->rows;

        $arguments = [
            'ad' => $customer[0]['firstname'],
            'soyad' => $customer[0]['lastname'],
        ];

        $jsonArguments = json_encode($arguments);

        $this->addQueue($customer[0]['telephone'], $templateId, $jsonArguments);
    }

    public function addQueue($phoneNumber, $templateId, $arguments){

        $this->db->query("INSERT INTO ".DB_PREFIX."sms_queue (date_added, status, arguments, template_id, phone_number) VALUES ( '".date('Y-m-d H:i:s')."', '1','".$arguments."','".$templateId."','".$phoneNumber."')");

    }




}
