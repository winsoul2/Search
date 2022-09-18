<?php
/**
 * Created by PhpStorm.
 * User: macmini2
 * Date: 17/12/2018 AD
 * Time: 16:24
 */

define("_SYSTEM_TTFONTS", "");
require_once ('tfpdf/tfpdf.php');

class LibTFPDF extends tFPDF
{
    function __construct()
    {
        parent::__construct();
        $CI =& get_instance();
    }

}