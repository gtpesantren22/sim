<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Data Dispensasi</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-shopping-bag"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Tanggungan</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm" onclick="window.location='<?= base_url('kasir/dispenAdd') ?>'"><i class="bx bx-plus"></i> Tambah Data Dispensasi</button>
                </div>
            </div>
        </div>
        <!--end breadcrumb-->
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>BP</th>
                                        <th>Sandal</th>
                                        <th>Tind. Lomba</th>
                                        <th>Tind. Wilayah</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($data as $ls_jns) {
                                        // $total = $this->db->query("SELECT SUM(nominal) as nm FROM pembayaran WHERE tahun = '$tahun' AND bulan = $ls_jns->bulan ")->row();
                                        // $jml = $this->db->query("SELECT COUNT(*) as jml FROM pembayaran WHERE tahun = '$tahun' AND bulan = $ls_jns->bulan ")->row();
                                    ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $ls_jns->nama; ?></td>
                                            <td><?= rupiah($ls_jns->bp); ?></td>
                                            <td><?= rupiah($ls_jns->sandal); ?></td>
                                            <td><?= rupiah($ls_jns->lomba); ?></td>
                                            <td><?= rupiah($ls_jns->wilayah); ?></td>
                                            <td>
                                                <a href="<?= base_url('kasir/delDispen/' . $ls_jns->id_dispensasi) ?>" class="btn btn-danger btn-sm tombol-hapus">Hapus</a>
                                            </td>
                                        </tr>
                                        <!-- Modal -->

                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->
    </div>
</div>
<!--end page wrapper -->