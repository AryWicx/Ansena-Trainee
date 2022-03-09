<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kasir extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->table = 'transaksi';
    }


    public function index(){
        $data = [
            'title' => "Kasir",
            'content' => "content/kasir",
        ];

		$this->load->view('template/index', $data);
	}

    public function get_data(){
        $data = $this->db->select('id, nama, harga_jual, gambar')->get('barang')->result_array();
        if($data) foreach ($data as $key => $value) {
            $data[$key]['gambar'] = file_exists('./assets/public/'.$value['gambar']) ? base_url('assets/public/'.$value['gambar']) : base_url('assets/default.png') ;
        }

        echo json_encode($data);
    }

    public function cart_add(){
        $new_cart = [
            'id' => $this->input->post('id'), 
            'qty' => 1, 
        ];

        $cart = $this->session->userdata('cart');
        $mines = $this->input->post('mines');
        
        $new_item = true;

        if($cart){
            foreach($cart as $key => $val){
                if($val['id'] == $this->input->post('id')){
                    if($mines == "true"){
                        if($cart[$key]['qty'] >= 1){
                            --$cart[$key]['qty'];
                        }
                    }else{
                        $cart[$key]['qty']++;
                    }
                    $new_item = false;
                }
            }
            
            if($new_item) $cart[] = $new_cart;
          
            $this->session->set_userdata('cart', $cart);
        }else{
            $this->session->set_userdata('cart', [$new_cart]);
        }

        echo json_encode('ok');
    }
    
    public function get_cart()
    {
        $cart = $this->session->userdata('cart');
        $id = [];
        if($cart){
            foreach ($cart as $key => $value) {
                $id[] = $value['id'];
            }
            
            $data = $this->db->select('id, nama, stok, harga_jual')->where_in('id', $id)->get('barang')->result_array();
            
            foreach ($data as $value) {
                $key = array_search($value['id'], array_column($cart, 'id'));
                $cart[$key]['nama'] = $value['nama'];
                $cart[$key]['harga_jual'] = $value['harga_jual'];

                $qty = $cart[$key]['qty'] < $value['stok'] ? $cart[$key]['qty'] : $value['stok'];

                $cart[$key]['qty'] = (int)$qty;
                $cart[$key]['subtotal'] = $value['harga_jual']*$qty;
            }

        }

        echo json_encode($cart);
    }

    public function checkout(){
        $cart = $this->session->userdata('cart');
        $item = $transaksi = $barang = [];
        $grand_qty = $grand_total = 0;
        if($cart){
            foreach ($cart as $value) { $id[] = $value['id']; }

            $data = $this->db->select('id, harga_jual, stok')->where_in('id', $id)->get('barang')->result_array();
           
            foreach ($data as $dkey => $value) {
                $key = array_search($value['id'], array_column($cart, 'id'));
                $qty = $cart[$key]['qty'] < $value['stok'] ? $cart[$key]['qty'] : $value['stok'];

                if($qty != 0){
                    $item[$dkey]['qty'] = $cart[$key]['qty'];
                    $item[$dkey]['barang_id'] = $value['id'];
                    $item[$dkey]['harga'] = $value['harga_jual'];
                    $item[$dkey]['subtotal'] = $value['harga_jual']*$cart[$key]['qty'];

                    $grand_qty += $cart[$key]['qty'];
                    $grand_total += $item[$dkey]['subtotal'];

                    $sisa_stok = $value['stok'] - $qty;
                    $barang[] = [
                        'id' => $value['id'],
                        'stok' => $sisa_stok,
                    ];
                }
            }

            $transaksi = [
                'kode' => date('Ymdhis'),
                'grand_qty' => $grand_qty,
                'grand_total' => $grand_total,
                'tanggal' => date('Y-m-d H:i:s'),
                'type' => "penjualan"
            ];
    
            $this->db->insert($this->table, $transaksi);
            $id = $this->db->insert_id();

            if($item) foreach ($item as $ikey => $value) {
                $item[$ikey]['transaksi_id'] = $id;
            }

            $this->db->insert_batch('item_'.$this->table, $item);
            $this->db->update_batch('barang', $barang, 'id');

            $this->clear_cart();
            
            $this->session->set_flashdata('massage', '<div class="alert alert-primary" role="alert">Transaksi berhasil, Transaksi dapat dilihat pada <a href="'.base_url('penjualan').'" class="alert-link">penjualan</a>.</div>');
        }else{
            $this->session->set_flashdata('massage', '<div class="alert alert-warning" role="alert"> Data tidak valid</div>');
        }

        redirect('kasir');
    }
    
    public function clear_cart(){
        $this->session->unset_userdata('cart');
    }


}