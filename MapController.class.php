<?php
namespace API\Controller;
use Think\Controller;
class MapController extends CommonController {
    private $point = array();
    private $areas = array(
        0=>array(
            array('x'=>116.36022,'y'=>40.028962),
            array('x'=>116.38336,'y'=>39.99879),
            array('x'=>116.384797,'y'=>39.993815),
            array('x'=>116.400069,'y'=>39.994368),
            array('x'=>116.400967,'y'=>39.974963),
            array('x'=>116.386846,'y'=>39.974493),
            array('x'=>116.386558,'y'=>39.929139),
            array('x'=>116.362663,'y'=>39.929388),
            array('x'=>116.36331,'y'=>39.918432),
            array('x'=>116.380414,'y'=>39.91893),
            array('x'=>116.380697,'y'=>39.876628),
            array('x'=>116.405994,'y'=>39.8784),
            array('x'=>116.407718,'y'=>39.838743),
            array('x'=>116.235675,'y'=>39.834089),
            array('x'=>116.194736,'y'=>39.930538),
            array('x'=>116.228923,'y'=>39.997906),
            ),
    );

    public function __construct($point) {
        // $this->config = $config;
        // $this->point = $point;
    }
    //获取配送时间
    public function getPeisongtime() {
        $address = I('get.address');
        $this->add2Point($address);
        foreach ($this->areas as $key => $val) {
            if($this->isIn($val)) {
                // header("Content-type: text/html; charset=utf-8");
                // echo "您的地址是：".$address.'</br>';
                // echo "您的配送时间是：";
                switch ($key) {
                    case '0':
                        $data = array(1,4);
                        $this->render_JSON($data);
                        break;

                    case '1':
                        $data = array(2,5);
                        $this->render_JSON($data);
                        break;

                    case '2':
                        $data = array(3,6);
                        $this->render_JSON($data);
                        break;

                    default:
                        echo "暂时无法通过系统确认配送时间，请联系客服人员";
                        break;
                }
            }
        }
        // header("Content-type: text/html; charset=utf-8");
        // echo "暂时无法通过系统确认配送时间，请联系客服人员";
    }

    //根据地址转换为百度坐标
    private function add2Point($address = '北京市海淀区上地十街10号') {
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, "http://api.map.baidu.com/geocoder/v2/?address=".$address."&output=json&ak=你的ak");  
        curl_setopt($ch, CURLOPT_HEADER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出  
        $result=curl_exec($ch);  
        curl_close($ch);
        $resObj = json_decode($result);
        $this->point['x'] = $resObj->result->location->lng;
        $this->point['y'] = $resObj->result->location->lat;
        return;
    }

    private function isIn($poly) {
        // $poly = $this->config[0];
        $polySides = count($poly);
        $x = $this->point['x'];
        $y = $this->point['y'];


        $i = $j = $polySides-1;
        $oddNodes = false;

        for ($i=0;$i<$polySides;$i++) {
            if (($poly[$i]['y']<$y && $poly[$j]['y']>=$y || $poly[$j]['y']<$y && $poly[$i]['y']>=$y)) {
                if ($poly[$i]['x'] + ($y-$poly[$i]['y'])/($poly[$j]['y']-$poly[$i]['y'])*($poly[$j]['x']-$poly[$i]['x'])<$x) {
                    $oddNodes = ! $oddNodes;
                } 
            }
            $j = $i;
        }
        return $oddNodes;
    }
}