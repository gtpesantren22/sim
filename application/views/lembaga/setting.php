<div class="flash-data" data-flashdata="<?= $this->session->flashdata('ok') ?>"></div>
<div class="flash-data-error" data-flashdata="<?= $this->session->flashdata('error') ?>"></div>

<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-md-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Setting</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-cog"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Settings</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card radius-10">
                    <div class="card-body">
                        <form action="" method="post">
                            <input type="hidden" name="pass_lama" value="<?= $user->password ?>" required>
                            <div class="box-body">
                                <div class="form-group mb-2">
                                    <label for="inputEmail3">Nama Lengkap</label>

                                    <input type="text" class="form-control" name="nama" value="<?= $user->nama ?>"
                                        required>

                                </div>
                                <div class="form-group mb-2">
                                    <label for="inputPassword3">Username</label>

                                    <input type="text" class="form-control" name="username"
                                        value="<?= $user->username ?>" required>

                                </div>
                                <div class="form-group mb-2">
                                    <label for="inputPassword3">Level</label>

                                    <input type="text" class="form-control" disabled
                                        value="Operator <?= $user->level ?>">

                                </div>
                                <div class="form-group mb-2">
                                    <label for="inputPassword3">Lembaga</label>

                                    <input type="text" class="form-control" value="<?= $lembaga->nama ?>" disabled
                                        required>

                                </div>
                                <hr>
                                <div class="form-group mb-2">
                                    <label for="inputPassword3">Password baru</label>

                                    <input type="password" class="form-control" name="newpass"
                                        placeholder="Password Baru">

                                </div>
                                <div class="form-group mb-2">
                                    <label for="inputPassword3">Confirm password</label>

                                    <input type="password" class="form-control" name="confir_newpass"
                                        placeholder="Konfirmasi Password Baru">

                                </div>
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <button type="submit" name="save_akun" class="btn btn-warning btn-sm">Update Info
                                    Akun</button>
                            </div><!-- /.box-footer -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->
    </div>
</div>
<!--end page wrapper -->
<!--end page wrapper -->