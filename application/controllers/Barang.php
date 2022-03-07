<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barang extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->table = 'barang';
    }

    public function index(){
        $data = [
            'title' => "Barang",
            'content' => "content/list",
            'links' => '<a type="button" href="'.base_url('barang/form').'" class="btn btn-primary">Tambah Barang</a>',
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
        $data = [
            'title' => "Barang",
            'content' => "content/form_barang",
        ];
        if($id){
            $data['data'] = $this->db->get_where('barang', ['id' => $id])->row_array();
        }
    
        $this->load->view('template/index', $data);
    }

    public function save(){
        $data = [
            'title' => "Barang",
            'content' => "content/form_barang",
        ];
        $id = $this->input->post('id');
        if($id){
            $data['data'] = $this->db->get_where('barang', ['id' => $id])->row_array();
        }
        
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('harga_jual', 'Harga Jual', 'required|is_numeric');
        $this->form_validation->set_rules('harga_beli', 'Harga Beli', 'required|is_numeric');

        if ($this->form_validation->run() == false) {
            $this->load->view('template/index', $data);
        } else {
            save_data();
        }
    }

    public function save_data(){
        $config['upload_path'] = './assets/public/';
        $config['allowed_types'] = 'png|jpg';
        $config['max_size'] = '20480';

        $this->load->library('upload', $config);

        $path= "./assets/public/";

        if(!$this->upload->do_upload('image')){
            $this->session->set_flashdata('massage', '<div class="alert alert-warning" role="alert" >Data berhasil disimpan, namun '.$this->upload->display_errors().'</div>');
        }else{
            $data['image'] = $this->upload->data('file_name');
            // $this->db->insert($this->table_user, $data);
        }

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
        $select = "tanggal_buat, nama, deskripsi, gambar, stok, harga_jual, harga_beli";

        if($this->input->get('tanggal')) $this->db->where('tanggal_buat', $this->input->get('tanggal'));
        $data = $this->Ap_db->getData($this->table, $select, $rowno, $perpage );
        return $data;
    }

}