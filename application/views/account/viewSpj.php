<div class="flash-data" data-flashdata="<?= $this->session->flashdata('ok') ?>"></div>
<div class="flash-data-error" data-flashdata="<?= $this->session->flashdata('error') ?>"></div>

<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Data SPJ Pengajuan</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-wallet"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Realisasi</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card radius-10">
                    <div class="card-header">
                        <!-- <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambah_bos<?= $spj->id_spj ?>"><i class="bx bx-check"></i>Setujui</button> -->
                        <!-- <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#tolak_bos<?= $spj->id_spj ?>"><i class="bx bx-no-entry"></i>Tolak</button> -->

                        <button class="btn btn-warning btn-sm" onclick="window.location='<?= base_url('account/spj') ?>'">Kembali</button>
                    </div>
                    <div class="card-body">
                        <iframe src="<?= 'https://simkupaduka.ppdwk.com/institution/spj_file/' . $spj->file_spj ?>" style="width: 100%; height: 500px;"></iframe>
                        <!-- <iframe src="<?= base_url('../simkupaduka-ok/institution/spj_file/05.0909.3.2022.pdf') ?>" style="width: 100%; height: 500px;"></iframe> -->
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->
    </div>
</div>
<!--end page wrapper -->

<!-- Modal Setujui BOS-->
<div class="modal fade" id="tambah_bos<?= $spj->id_spj ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form
                    Persetujuan SPJ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('account/setujuiSpj'); ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $spj->id_spj; ?>">
                    <input type="hidden" name="kode" value="<?= $spj->kode_pengajuan; ?>">
                    <input type="hidden" name="hp" value="<?= $spj->hp; ?>">
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Lembaga <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="first-name" name="nm_lm" required="required" readonly value="<?= $spj->nama ?>" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Periode <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="first-name" required="required" disabled value="<?= $bulan[$spj->bulan] . ' ' . $spj->tahun ?>" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Nominal <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="first-name" name="cair" required="required" value="<?= rupiah($jml->jml_cair) ?>" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Tanggal disetujui <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="date" required="required" name="tgl" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Menyetujui <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" required="required" name="user" value="<?= $user->nama ?>" class="form-control" readonly>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="save" class="btn btn-success"><i class="bx bx-check"></i>Setujui</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Modal Tolak BOS-->
<div class="modal fade" id="tolak_bos<?= $spj->id_spj ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form
                    Penolakan SPJ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('account/tolakSpj'); ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $spj->id_spj; ?>">
                    <input type="hidden" name="kode" value="<?= $spj->kode_pengajuan; ?>">
                    <input type="hidden" name="hp" value="<?= $spj->hp; ?>">
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Lembaga <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" name="nm_lm" id="first-name" readonly value="<?= $spj->nama ?>" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Periode <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="first-name" required="required" disabled value="<?= $bulan[$spj->bulan] . ' ' . $spj->tahun ?>" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Nominal <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="first-name" required="required" disabled value="<?= rupiah($jml->jml_cair) ?>" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Tanggal penolakan <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="date" required="required" name="tgl" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Catatan Penolakan <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                            <textarea id="" required="required" name="isi" class="form-control "></textarea>
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Menolaks <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" required="required" name="user" value="<?= $user->nama ?>" class="form-control" readonly>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger"><i class="bx bx-check"></i>Tolak Now</button>
                </div>
            </form>

        </div>
    </div>
</div>