<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembelian extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->table = 'transaksi';
    }

    public function index(){
        $data = [
            'title' => "Pembelian",
            'content' => "content/list",
            'links' => '<a type="button" href="'.base_url('pembelian/form').'" class="btn btn-primary">Pembelian</a>',
            'show' => [
                'no', 'tanggal', 'kode', 'jumlah_item' , 'grand_total', 'action'
            ]
        ];

		$this->load->view('template/index', $data);
	}

    public function detail($id = ''){
        $out = [];
        $total = 0;

        $data = $this->db->select('nama, item_transaksi.* ')
        ->join('barang', 'item_transaksi.barang_id = barang.id')
        ->get_where('item_transaksi', ['transaksi_id' => $id])
        ->result_array();

        
        if($data) foreach ($data as $key => $value) {
            $out[] = [
                'nama' => $value['nama'],
                'harga' => $value['harga'],
                'subtotal' => $value['subtotal'],
                'qty' => $value['qty'],
            ];
            $total += $value['subtotal'];
        }

        $out[] = ['nama' => "Grandtotal Amt", 'subtotal' => $total];

        echo json_encode($out);
    }

    public function getBarang(){
        if($this->input->get('s')) $this->db->like('nama', $this->input->get('s', true));
        $data = $this->db->select('id, nama as text, harga_beli as harga')
        ->get('barang')->result_array();
        echo json_encode($data);
    }

    public function form(){
        $data = [
            'title' => "Stok Barang",
            'content' => "content/form_pembelian",
        ];
		$this->load->view('template/index', $data);
    }

    public function save(){
        $data = [];
        $grand_qty = $grandtotal = 0;
        $id = $this->input->post('id');
        $harga = $this->input->post('harga');
        $qty = $this->input->post('qty');

        if($id){
            foreach ($id as $key => $value) {
                $item[] = [
                    'barang_id' => $value,
                    'harga' => $harga[$key],
                    'qty' => $qty[$key],
                    'subtotal' => $harga[$key]*$qty[$key],
                ];

                $this->db->set('stok', 'stok+'.$qty[$key], FALSE)
                ->where(['id' => $value])
                ->update('barang');
    
                $grand_qty = $grand_qty + $qty[$key];
                $grandtotal = $grandtotal + ($qty[$key]*$harga[$key]);
            }
    
            $transaksi = [
                'kode' => date('Ymdhis'),
                'grand_qty' => $grand_qty,
                'grand_total' => $grandtotal,
                'tanggal' => date('Y-m-d H:i:s'),
                'type' => "pembelian"
            ];
    
    
            $this->db->insert($this->table, $transaksi);
            $id = $this->db->insert_id();
    
            if($item) foreach ($item as $ikey => $value) {
                $item[$ikey]['transaksi_id'] = $id;
            }
    
            $this->db->insert_batch('item_'.$this->table, $item);
            $this->session->set_flashdata('massage', '<div class="alert alert-primary" role="alert">Transaksi berhasil, Transaksi dapat dilihat pada <a href="'.base_url('pembelian').'" class="alert-link">pembelian</a>.</div>');
        }else{
            $this->session->set_flashdata('massage', '<div class="alert alert-primary" role="alert">Transaksi berhasil, Transaksi dapat dilihat pada <a href="'.base_url('pembelian').'" class="alert-link">pembelian</a>.</div>');
        }

        redirect('pembelian/form');
    }
    
    public function delete($id = ''){
        if(!$id) { exit(json_encode('ok')); };
        
        $update_barang = [];
        $old = $this->db->get_where('item_transaksi',['transaksi_id' => $id])->result_array();

        if($old) foreach($old as $val){
            $this->db->set('stok', 'stok-'.$val['qty'], FALSE)
            ->where(['id' => $val['barang_id']])
            ->update('barang');
        }

        $this->db->where(['id' => $id])->delete('transaksi');
        $this->db->where(['transaksi_id' => $id])->delete('item_transaksi');
        
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
        $select = "tanggal, kode, jumlah_item , grand_total, transaksi.id";
        
        if($this->input->get('tanggal')) $this->db->where('DATE_FORMAT(tanggal, "%Y-%m-%d") =', $this->input->get('tanggal'));
        $this->db->where('type','pembelian');
        
        $join = [
            '(SELECT COUNT(*) jumlah_item, transaksi_id from item_transaksi GROUP BY transaksi_id) item_transaksi' => 'item_transaksi.transaksi_id = transaksi.id'
        ];

        $data = $this->Ap_db->getData($this->table, $select, $rowno, $perpage, $join );
        if($data) foreach ($data as $key => $value) {
            $data[$key]['action'] = '
            <button 
            type="button" 
            class="btn btn-primary btn-sm btn-detail" 
            data-bs-toggle="modal" 
            data-bs-target="#myModal" 
            data-link="'.base_url("pembelian/detail/".$value["id"]).'"><i class="fas fa-eye"></i></button>
            
            <button 
            type="button" 
            class="btn btn-secondary btn-sm btn-delete" 
            data-bs-toggle="modal" 
            data-bs-target="#myModal" 
            data-nama="'.$value["kode"].'"
            data-link="'.base_url("pembelian/delete/".$value["id"]).'"><i class="fas fa-trash"></i></button>';
            
        }
        return $data;
    }
}