<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->table = 'transaksi';
    }

    public function index(){
        $data = [
            'title' => "Transaksi",
            'content' => "content/list",
            'links' => '<a type="button" href="'.base_url('penjualan/form').'" class="btn btn-primary mb-3">Penjualan</a>',
            'show' => [
                'no',
                'tanggal',
                'nama',
                'description',
                'gambar',
                'stok',
                'harga_jual',
                'harga_beli'
            ]
        ];

		$this->load->view('template/index', $data);
	}

    public function form($id = ''){
                
    }

    public function load_data($rowno=0){
        $perpage = 10;
        if($rowno != 0){
        $rowno = ($rowno-1) * $perpage;
        }
    
        $allcount = $this->Ap_db->getrecordCount($this->table);

        // Get data
        $users_record = $this->get_data($rowno, $perpage);
        
    
        // Pagination Configuration
        $config = $this->config->item('pagination');
        $config['base_url'] = base_url().'barang/load_data';
        $config['total_rows'] = $allcount;
        $config['per_page'] = $perpage;

        // Initialize
        $this->pagination->initialize($config);

        // Initialize $data Array
        $data['pagination'] = $this->pagination->create_links();
        $data['result'] = $users_record;
        $data['row'] = $rowno;

        echo json_encode($data);
    
    }

    public function get_data($rowno, $perpage) {
        $select = "";
        $data = $this->Ap_db->getData($this->table, $rowno, $perpage, $select );
        return $data;
    }
}