<?php
//$con = mysqli_connect('dev.srusct.or.th', 'srusctdev_com', 'Jy33wZMZy', 'srusctdev_com');

class Beneficiary_upload extends CI_Controller
{
    function __construct()
    {
        parent::__construct();

    }


    //เเสดงผล
    function index()
    {
        $fileList = $this->session->flashdata('fileList');

        //เอาไว้ใช้ตอนที่ยังไม่ได้เอาไฟล์เขา เพราะ หาไฟล์ไม่เจอจะ err
        if (is_null($fileList)) {
            $fileList = array() ;
        }
        $this->load->view('beneficiary_upload/index', array(
            'files' => $fileList,
            'errorMessage' => $this->input->get('error_message')
        ));
    }

    //เเสดงหน้าเเรก (อัพโหลดไฟล์)
    function index2()
    {
        $pdfFiles = $_FILES['files'];
        $config['upload_path'] = FCPATH . 'assets/uploads/benefits_attach';
        $config['allowed_types'] = 'pdf';
        $config['overwrite'] = TRUE; //ทับไฟล์
        $this->load->library('upload', $config);



        $fileList = array();

        //ตรวจสอบไฟล์ที่ไม่ใช้ .pdf
        foreach ($pdfFiles['name'] as $key => $file) {
            if  ($pdfFiles['type'][$key] != "application/pdf"){
                echo "<script type='text/javascript'>alert('มีไฟล์ที่ไม่ใช้ .pdf กรุณาลองใหม่');</script>";
                exit();
            }
            $_FILES['pdf_upload']['type'] = $pdfFiles['type'][$key];

        }

        foreach ($pdfFiles['name'] as $key => $file) {
            $_FILES['pdf_upload']['name'] = $pdfFiles['name'][$key];
            $_FILES['pdf_upload']['type'] = $pdfFiles['type'][$key];
            $_FILES['pdf_upload']['tmp_name'] = $pdfFiles['tmp_name'][$key];
            $_FILES['pdf_upload']['error'] = $pdfFiles['error'][$key];
            $_FILES['pdf_upload']['size'] = $pdfFiles['size'][$key];

            $fileList[] = $pdfFiles['name'][$key];

            $this->upload->do_upload('pdf_upload');
            $memberId = explode('.', $pdfFiles['name'][$key]);
            $this->db->set("benefits_attach", $pdfFiles['name'][$key]);
            $this->db->where("member_id", $memberId[0]);
            $this->db->update("coop_mem_apply");
        }

        $this->session->set_flashdata('fileList', $fileList);

        redirect('Beneficiary_upload');
        exit;
    }
}

