<?php
$config = array(
	0=>array(
		array('x'=>116.34419,'y'=>39.998559),
		array('x'=>116.402975,'y'=>39.999664),
		array('x'=>116.401107,'y'=>39.983191),
		array('x'=>116.380122,'y'=>39.930152),
		array('x'=>116.331614,'y'=>39.952169),
	),
);

// $point = array('x'=>floatval($_GET['x']),'y'=>floatval($_GET['y']));
class area{
	//待判断的所有区域
	private $config = array();
	//待判断的点
	private $point = array();

	public function __construct($config, $point) {
		$this->config = $config;
		$this->point = $point;
	}

	public function isIn() {
		$poly = $this->config[0];
		$polySides = count($this->config[0]);
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
		var_dump($oddNodes);
		return $oddNodes;
	}
}
$point = array('x'=>116.389562,'y'=>39.951746);
$a = new area($config,$point);
$a->isIn();