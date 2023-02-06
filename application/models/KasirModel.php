<?php
defined('BASEPATH') or exit('No direct script access allowed');

class KasirModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->db2 = $this->load->database('dekos',true);
    }
    function apikey()
    {
        $this->db->select('*');
        $this->db->from('api');
        $this->db->where('nama', 'Bendahara');
        return $this->db->get();
    }

    function getBy($table, $where1, $dtwhere1)
    {
        $this->db->where($where1, $dtwhere1);
        return $this->db->get($table);
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
    
    public function getPengajuanDisp($tahun)
    {
        $this->db->from('pengajuan');
        $this->db->join('lembaga', 'ON pengajuan.lembaga=lembaga.kode');
        $this->db->where('lembaga.tahun', $tahun);
        $this->db->where('pengajuan.tahun', $tahun);
        $this->db->like('kode_pengajuan', 'DISP.');
        return $this->db->get();
    }
    
    function getBySum($table, $where, $dtwhere, $sum)
    {
        $this->db->select('*');
        $this->db->select_sum($sum, 'jml');
        $this->db->where($where, $dtwhere);
        return $this->db->get($table);
    }
    
    public function update($table, $data, $where, $dtwhere)
    {
        $this->db->where($where, $dtwhere);
        $this->db->update($table, $data);
    }
    
    public function delete($table, $where, $dtwhere)
    {
        $this->db->where($where, $dtwhere);
        $this->db->delete($table);
    }

    public function deleteBayar($nis, $bulan, $tahun)
    {
        $this->db2->where('nis', $nis);
        $this->db2->where('bulan', $bulan);
        $this->db2->where('tahun', $tahun);
        $this->db2->delete('kos');
    }

    public function input($tbl, $data)
    {
        $this->db->insert($tbl, $data);
    }
    public function inputDb2($tbl, $data)
    {
        $this->db2->insert($tbl, $data);
    }

    function getByJoin($table1, $table2, $on1, $on2, $where1, $dtwhere1)
    {
        $this->db->from($table1);
        $this->db->join($table2, 'ON '.$table1.'.'.$on1.' = '.$table2.'.'.$on2);
        $this->db->where($where1, $dtwhere1);
        return $this->db->get();
    }

    public function updateDb2($table, $data, $where, $dtwhere)
    {
        $this->db2->where($where, $dtwhere);
        $this->db2->update($table, $data);
    }
}