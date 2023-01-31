<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LembagaModel extends CI_Model
{

    public function __construct()
    {
    }

    function apikey()
    {
        $this->db->select('*');
        $this->db->from('api');
        $this->db->where('nama', 'Bendahara');
        return $this->db->get();
    }

    function input($tbl, $data)
    {
        $this->db->insert($tbl, $data);
    }

    public function update($table, $data, $where, $dtwhere)
    {
        $this->db->where($where, $dtwhere);
        $this->db->update($table, $data);
    }

    public function update2($table, $data, $where1, $dtwhere1, $where2, $dtwhere2)
    {
        $this->db->where($where1, $dtwhere1);
        $this->db->where($where2, $dtwhere2);
        $this->db->update($table, $data);
    }

    public function delete($table, $where, $dtwhere)
    {
        $this->db->where($where, $dtwhere);
        $this->db->delete($table);
    }

    function getBy($table, $where, $dtwhere)
    {
        $this->db->where($where, $dtwhere);
        return $this->db->get($table);
    }


    function getBySum2($table, $where1, $dtwhere1, $where2, $dtwhere2, $sum)
    {
        $this->db->select('*');
        $this->db->select_sum($sum, 'jml');
        $this->db->where($where1, $dtwhere1);
        $this->db->where($where2, $dtwhere2);
        return $this->db->get($table);
    }

    public function getPjn($table, $lembaga, $tahun)
    {
        $this->db->from($table);
        $this->db->where('lembaga', $lembaga);
        $this->db->where('tahun', $tahun);
        $this->db->order_by('no_urut', 'DESC');
        $this->db->limit('1');
        return $this->db->get();
    }

    public function getTotalRabJenis($jenis, $lm, $tahun)
    {
        $this->db->select_sum('total');
        $this->db->where('jenis', $jenis);
        $this->db->where('lembaga', $lm);
        $this->db->where('tahun', $tahun);
        return $this->db->get('rab');
    }

    public function getTotalRealJenis($jenis, $lm, $tahun)
    {
        $this->db->select_sum('nominal');
        $this->db->where('jenis', $jenis);
        $this->db->where('lembaga', $lm);
        $this->db->where('tahun', $tahun);
        return $this->db->get('realis');
    }

    function getBy2($table, $where1, $dtwhere1, $where2, $dtwhere2)
    {
        $this->db->where($where1, $dtwhere1);
        $this->db->where($where2, $dtwhere2);
        return $this->db->get($table);
    }

    function getBySum($table, $where, $dtwhere, $sum)
    {
        $this->db->select('*');
        $this->db->select_sum($sum, 'jml');
        $this->db->where($where, $dtwhere);
        return $this->db->get($table);
    }

    public function getPengajuan($lembaga, $tahun)
    {
        $this->db->where('lembaga', $lembaga);
        $this->db->where('tahun', $tahun);
        $this->db->not_like('kode_pengajuan', 'DISP.');
        $this->db->order_by('no_urut', 'DESC');
        return $this->db->get('pengajuan');
    }
    public function getPengajuan2($lembaga, $tahun)
    {
        $this->db->where('lembaga', $lembaga);
        $this->db->where('tahun', $tahun);
        $this->db->like('kode_pengajuan', 'DISP.');
        $this->db->order_by('no_urut', 'DESC');
        return $this->db->get('pengajuan');
    }

    public function getSpj($lembaga, $tahun)
    {
        $this->db->select('spj.*,  pengajuan.cair as b_cair, pengajuan.kode_pengajuan as b_kode');
        $this->db->from('spj');
        $this->db->join('pengajuan', 'ON  spj.kode_pengajuan=pengajuan.kode_pengajuan');
        $this->db->where('spj.lembaga', $lembaga);
        $this->db->where('spj.tahun', $tahun);
        return $this->db->get();
    }
}