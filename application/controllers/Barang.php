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
                'harga_beli',
                'action',
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
        }else{
            if(empty($_FILES['gambar']['name'])){
                $this->form_validation->set_rules('gambar', 'Gambar Produk', 'required');
            }
        }
        
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('harga_jual', 'Harga Jual', 'required|is_numeric');
        $this->form_validation->set_rules('harga_beli', 'Harga Beli', 'required|is_numeric');

        if ($this->form_validation->run() == false) {
            $data['data'] = $this->input->post();
            $this->load->view('template/index', $data);
        } else {
            $this->save_data();
        }
    }

    public function save_data(){
        $data = [
            'title' => "Barang",
            'content' => "content/form_barang",
        ];
        $id = $this->input->post('id');
        if($id){
            $data['data'] = $this->db->get_where('barang', ['id' => $id])->row_array();
        }

        $config['upload_path'] = './assets/public/';
        $config['allowed_types'] = 'png|jpg';
        $config['max_size'] = '2048';

        $this->upload->initialize($config);
        $path= "./assets/public/";

        if($_FILES['gambar']['name'] && !$this->upload->do_upload('gambar')){
            $data['data'] = $this->input->post();
            $this->session->set_flashdata('massage', '<div class="alert alert-warning" role="alert" > Failed to upload file, '.$this->upload->display_errors().'</div>');
            $this->load->view('template/index', $data);
        }else{
            $barang = [
                'nama' => $this->input->post('nama'),
                'deskripsi' => $this->input->post('deskripsi'),
                'stok' => $this->input->post('stok'),
                'harga_jual' => $this->input->post('harga_jual'),
                'harga_beli' => $this->input->post('harga_jual'),
            ];

            if($this->upload->data()){ $barang['gambar'] = $this->upload->data('file_name'); }
            
            if(isset($data['data'])){ 
                unset($barang['stok']);
                if($_FILES['gambar']['name'] && file_exists($path.$data['data']['gambar'])){
                    unlink($path.$data['data']['gambar']);
                }
                $this->db->where('id', $id)->update($this->table, $barang);
                $this->session->set_flashdata('massage', '<div class="alert alert-success" role="alert"> Record update successfully</div>');
                redirect('barang');
            }else{
                $barang['tanggal_buat'] = date('Y-m-d H:i:s');
                $this->db->insert($this->table, $barang);
                $this->session->set_flashdata('massage', '<div class="alert alert-success" role="alert"> Record added successfully</div>');
                redirect('barang');
            }

        }
    }

    public function delete($id = ''){
        $old = $this->db->get_where('barang', ['id' => $id])->row_array();
        $path= "./assets/public/";

        if($_FILES['gambar']['name'] && file_exists($path.$old['gambar'])){
            unlink($path.$old['gambar']);
        }
        $this->db->where('id', $id)->delete($this->table);
        exit(json_encode('ok'));
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
        $select = "id, tanggal_buat, nama, deskripsi, gambar, stok, harga_jual, harga_beli";

        if($this->input->get('tanggal')) $this->db->where('tanggal_buat', $this->input->get('tanggal'));
        $data = $this->Ap_db->getData($this->table, $select, $rowno, $perpage );
        if($data) foreach ($data as $key => $value) {
            $data[$key]['gambar'] = file_exists("./assets/public/".$value["gambar"]) ? '<a href="'.base_url("assets/public/".$value["gambar"]).'" target="_blank">Link</a>' : 'No image';
            $data[$key]['action'] = '
            <a href="'.base_url("barang/form/".$value["id"]).'" class="btn btn-primary btn-sm btn-delete" ><i class="fas fa-edit"></i></a>
            <button 
            type="button" 
            class="btn btn-secondary btn-sm btn-delete" 
            data-bs-toggle="modal" 
            data-bs-target="#myModal" 
            data-nama="'.$value["nama"].'"
            data-link="'.base_url("barang/delete/".$value["id"]).'"><i class="fas fa-trash"></i></button>';
            
            unset($data[$key]['id']);
        }

        return $data;
    }

}