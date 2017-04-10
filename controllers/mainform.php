<?php 

class mainform extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('mainform_model');
		$this->load->library('session');
	}


	public function index()
	{

		// $id = TRADER_ID; // dummy - to be received from investroom 
		
		if ($this->uri->segment(3) == '')
		{
		   $id= $this->config->item('TRADER_ID');
		   $TRADER_ID = $id;
		}
		else
		{
		   $id = $this->uri->segment(3);
	 	   $this->config->set_item('TRADER_ID', $id);
		   $TRADER_ID = $this->config->item('TRADER_ID');
		}
		$this->session->set_userdata('TRADER_ID',$TRADER_ID);

		// get wallets for all currencies

		$wallet = $this->mainform_model->get_wallet($id); 
		$data['wallet'] = (array) $wallet;

		$data['id'] = $id;  // to be developed during the integration
		$data['from'] = 0;  // signal to the view

		// get quotes for all currencies

		$data['quotes'] = $this->mainform_model->mainform_get_quotes(); 

		// get "PAMM accounts rating" form data
		$data['rpa']    = (array) $this->mainform_model->get_rating(); 

		// get "Manage PAMM accounts" form data
		$data['mpa']    = (array) $this->mainform_model->get_manage_pamm_accounts($TRADER_ID);
		$data['jpa']    =         $this->mainform_model->get_invested_accounts($TRADER_ID);
		$data['TRADER_ID'] = $TRADER_ID;

		// to open the manager tab
		if ($this->uri->segment(4) == 'rm')
		{
		   $data['from'] = 33;	
		}

		// to open the investor tab
		if ($this->uri->segment(4) == 'ri')
		{
		   $data['from'] = 35;	
		}

		$this->load->view('main_form',$data);
	}
	public function graph($acc_number)
	{

		$data_prev  =  $this->mainform_model->get_graph_data($acc_number);
		if (count($data_prev) == 0)
			return "nopicture.png";
		$W=129;
		$H=41;

		$i = 0;
		foreach ($data_prev as $value)
		{
		         $DATA[$i] = $value->pamm_tp_total;
		         $i++;
		}



		array_shift($DATA);			// to avoid first zero period
//		print_r($DATA);

		// Отступы
		$MB=10;  // Нижний
		$ML=0;   // Левый 
		$M=2;    // Верхний и правый отступы.


		$count=count($DATA);

		if ($count == 0)
		{

		  $DATA[0] = 1;
 		  $count=count($DATA);
		 
		}
		$max=0;         
		for ($i=0;$i<$count;$i++) {
		    $max=$max<$DATA[$i]?$DATA[$i]:$max;
		    }           	

		if ($max == 0)
			{
			 $max = 1;
			}
		$max=intval($max+($max/10));

		$im=imagecreate($W,$H);

		$bg[0]=imagecolorallocate($im,255,255,255);

		$bar=imagecolorallocate($im,32,187,43);

		$text_width=0;

		$ML+=$text_width;

		$RW=$W-$ML-$M;
		$RH=$H-$MB-$M;  

		$X0=$ML;

		$Y0=$H-$MB;     

		$dx=($RW/$count)/2;

		$pu=$Y0-($RH/$max*$DATA[0]);
		$px=intval($X0+$dx);

		for ($i=1;$i<$count;$i++) {
		    $x=intval($X0+$i*($RW/$count)+$dx);

		    $y=$Y0-($RH/$max*$DATA[$i]);
		    imageline($im,$px,$pu,$x,$y,$bar);
		    $pu=$y;
		    $px=$x;
		    }

		header("Content-Type: image/png");

		ImagePNG($im);

		imagedestroy($im);

	}
	public function graph1($acc_number)
	{
	$month_profitable = $this->mainform_model->obtain_month_profitable($acc_number);
	$i = 0;

		foreach ($month_profitable as $value)
		{
		         $values[$i]['value'] = $value->ptp;
		         $values[$i]['month'] = $value->month;
		         $i++;
		}
//	print_r($values);

	// Get the total number of columns we are going to plot

	    $columns  = count($values);

	// Get the height and width of the final image

	    $width = 300;
	    $height = 400;
	    $h_w_l  = 200;
	    $w_w_l  = 250;

	// Set the amount of space between each column

	    $padding = 35;

	// Get the width of 1 column

	    $column_width = 50;

	// Generate the image variables

	    $im        = imagecreate($width,$height);
	    $gray      = imagecolorallocate ($im,0x8c,0xc6,0x3f);
	    $gray_lite = imagecolorallocate ($im,0xee,0xee,0xee);
	    $gray_dark = imagecolorallocate ($im,0x7f,0x7f,0x7f);
	    $white     = imagecolorallocate ($im,0xff,0xff,0xff);
	    $black     = imagecolorallocate($im, 0x00, 0x00, 0x00);
	    $font ="";
    
	// Fill in the background of the image

	    imagefilledrectangle($im,0,0,$width,$height,$white);
    
	    $maxv = 0;

	// Calculate the maximum value we are going to plot

	    for($i=0;$i<$columns;$i++)$maxv = max($values[$i]['value'],$maxv);

	    $minv = 0;

	// Calculate the mimimum value we are going to plot
	                           
	    for($i=0;$i<$columns;$i++)$minv = min($values[$i]['value'],$minv);

	// Now plot each column
        if ($maxv != 0 || $minv != 0)
	{
	    for($i=0;$i<$columns;$i++)
	    {
		
		if ($values[$i]['value'] > 0)
		{
		        $column_height = ($h_w_l / 100) * (( $values[$i]['value'] / $maxv) *100);

		        $x1 = ($i+1)*$column_width;
		        $y1 = $h_w_l-$column_height;
		        $x2 = (($i+2)*$column_width)-$padding;
		        $y2 = $h_w_l;

		        imagefilledrectangle($im,$x1,$y1,$x2,$y2,$gray);
			imagestring($im,2,$x1,$h_w_l+30,$values[$i]['month'],$black);
	        }
	       elseif ($values[$i]['value'] < 0)
		{
		        $column_height = ($h_w_l / 100) * (( abs($values[$i]['value']) / abs($minv)) *100);

		        $x1 = ($i+1)*$column_width;
		        $y1 = $h_w_l;
		        $x2 = (($i+2)*$column_width)-$padding;
		        $y2 = $h_w_l + $column_height;


		        imagefilledrectangle($im,$x1,$y1,$x2,$y2,$gray);
			imagestring($im,2,$x1,$h_w_l-30,$values[$i]['month'],$black);
	        }
	       elseif ($values[$i]['value'] == 0)
		{
		        $column_height = 0;

		        $x1 = ($i+1)*$column_width;
		        $y1 = $h_w_l;
		        $x2 = (($i+2)*$column_width)-$padding;
		        $y2 = $h_w_l + $column_height;


		        imagefilledrectangle($im,$x1,$y1,$x2,$y2,$gray);
			imagestring($im,2,$x1,$h_w_l+30,$values[$i]['month'],$black);
	        }

	    }
	}
	        imageline($im,40,0,40,$h_w_l,$gray_dark);
	        imageline($im,40,$h_w_l,$w_w_l,$h_w_l,$gray_dark);
        if ($maxv > 0)
	{

		for($i=0;$i<6;$i++)
		{
			imagestring($im,2,0,$i*40,round((5-$i)*100*($maxv/5),2)."%",$black);
		}
	}
	if ($minv < 0)
	{
	        imageline($im,40,0,40,400,$gray_dark);
		for($i=0;$i<6;$i++)
		{
			imagestring($im,2,0,$h_w_l+($i*40),round($i*100*($minv/5),2)."%",$black);
		}
	}

	// Send the PNG header information. Replace for JPEG or GIF or whatever

	    header ("Content-type: image/png");
	    imagepng($im);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/mainform.php */