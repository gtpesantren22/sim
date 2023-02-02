<?php
defined('BASEPATH') or exit('No direct script access allowed');

class KasirModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
       
    }
    function apikey()
    {
        $this->db->select('*');
        $this->db->from('api');
        $this->db->where('nama', 'Bendahara');
        return $this->db->get();
    }
    
    function getBy2($table, $where1, $dtwhere1, $where2, $dtwhere2)
    {
        $this->db->where($where1, $dtwhere1);
        $this->db->where($where2, $dtwhere2);
        return $this->db->get($table);
    }
    
    public function getPengajuan($tahun)
    {
        $this->db->from('pengajuan');
        $this->db->join('lembaga', 'ON pengajuan.lembaga=lembaga.kode');
        $this->db->where('lembaga.tahun', $tahun);
        $this->db->where('pengajuan.tahun', $tahun);
        $this->db->not_like('kode_pengajuan', 'DISP.');
        return $this->db->get();
    }
}