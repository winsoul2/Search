<?php
/**
 * Created by PhpStorm.
 * User: macmini2
 * Date: 2019-10-16
 * Time: 16:16
 */

class Forecast_loan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        $arr_data = array();
        $this->libraries->template('forecast_loan/index',$arr_data);
    }


}
