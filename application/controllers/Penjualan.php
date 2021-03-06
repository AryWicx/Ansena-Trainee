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
            'links' => '<a type="button" href="'.base_url('kasir').'" class="btn btn-primary">Penjualan</a>',
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
    
    public function delete($id = ''){
        $old = $this->db->get_where('item_transaksi',['transaksi_id' => $id])->result_array();
        $update_barang = [];

        if($old) foreach($old as $val){
            $this->db->set('stok', 'stok+'.$val['qty'], FALSE)
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
        $this->db->where('type','penjualan');
        
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
            data-link="'.base_url("penjualan/detail/".$value["id"]).'"><i class="fas fa-eye"></i></button>
            <button 
            type="button" 
            class="btn btn-secondary btn-sm btn-delete" 
            data-bs-toggle="modal" 
            data-bs-target="#myModal" 
            data-nama="'.$value["kode"].'"
            data-link="'.base_url("penjualan/delete/".$value["id"]).'"><i class="fas fa-trash"></i></button>';
            
        }
        return $data;
    }
}