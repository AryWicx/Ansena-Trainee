<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kasir extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->table = 'barang';
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
            
            $data = $this->db->select('id, nama, harga_jual')->where_in('id', $id)->get('barang')->result_array();
            
            foreach ($data as $key => $value) {
                $key = array_search($value['id'], array_column($cart, 'id'));
                $cart[$key]['nama'] = $value['nama'];
                $cart[$key]['harga_jual'] = $value['harga_jual'];
                $cart[$key]['subtotal'] = $value['harga_jual']*$cart[$key]['qty'];
                $cart[$key]['nama'] = $value['nama'];
            }

            
        }

        echo json_encode($cart);
    }

    public function checkout(){
        
        $cart = $this->session->userdata('cart');
        $item = $transaksi = [];
        $grand_qty = $grand_total = 0;
        if($cart){
            foreach ($cart as $key => $value) {
                $id[] = $value['id'];
            }
            $data = $this->db->select('id, harga_jual')->where_in('id', $id)->get('barang')->result_array();
           
            // kode 	grand_qty 	grand_total 	tanggal 	type 
            // id 	transaksi_id 	barang_id 	harga 	qty 	subtotal 
            foreach ($data as $key => $value) {
                $key = array_search($value['id'], array_column($cart, 'id'));
                if($cart[$key]['qty'] != 0){
                    $item[$key]['qty'] = $cart[$key]['qty'];
                    $item[$key]['barang_id'] = $value['id'];
                    $item[$key]['harga'] = $value['harga_jual'];
                    $item[$key]['subtotal'] = $value['harga_jual']*$cart[$key]['qty'];

                    $grand_qty += $cart[$key]['qty'];
                    $grand_total += $item[$key]['subtotal'];
                }
            }
        }

        $transaksi = [

        ];
        echo '<pre>';
        print_r($item);
        echo '</pre>';
    }

    public function clear_cart(){
        $this->session->unset_userdata('cart');
    }


}