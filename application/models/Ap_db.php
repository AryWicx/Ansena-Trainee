<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Ap_db extends CI_Model {

  public function __construct() {
    parent::__construct(); 
  }

  // Fetch records
  public function getData($table,$select = '*',$rowno,$rowperpage) {
 
    $this->db->select($select);
    $this->db->from($table);
    $this->db->limit($rowperpage, $rowno);
    $this->db->order_by('id', "DESC");
    $query = $this->db->get();
 
    return $query->result_array();
  }

  // Select total records
  public function getrecordCount($table) {

    $this->db->select('count(*) as allcount');
    $this->db->from($table);
    $query = $this->db->count_all_results();
 
    return $query ;
  }

}