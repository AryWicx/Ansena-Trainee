<!doctype html>
<html>
    <head>
        <title><?= isset($title) ? $title : "" ?> - Ansena Trainee</title>
        <meta charset="UTF-8">  
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    </head>
    <body>
      <section class="section-navbar">
        <div class="col-sm-12">
          <nav class="navbar navbar-expand-lg navbar navbar-dark bg-dark">
              <div class="container-fluid navbar-container">
                  <a class="navbar-brand" href="<?= base_url() ?>">
                  <span class="fa-solid fa-shopping-cart fa-sm"></span>
                  Ansena Trainee
                  </a>
                <button class="navbar-toggler menu-navbar" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="fa-solid fa-bars fa-sm"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                  <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php 
                    $navbar = ['barang', 'penjualan', 'pembelian'];
                    foreach ($navbar as $key => $value) : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $value == $this->uri->segment(1) ? 'active' : ''  ?>" aria-current="page" href="<?= base_url($value) ?>"><?= $value ? ucfirst($value) : "Barang" ?></a>
                    </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            </nav>
          </div>
          <div class="nav-title">
            <div class="container">
              <div class="row">
                <div class="col-lg-12 align-items-center d-flex justify-content-between">
                  <h5><b><?= isset($title) ? $title : "" ?></b></h5>
                  <?= isset($links) ? ucfirst($links) : "" ?>
                </div>
              </div>
            </div>
          </div>
      </section>

      <?php if(isset($content)) $this->load->view($content); ?>

      <div class="modal fade" id="myModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title title" id="staticBackdropLabel">Konfirmasi</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body message"></div>
            <div class="modal-footer action">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" id="ok" class="btn btn-primary">Okey</button>
            </div>
          </div>
        </div>
      </div>
    </body>

    <script src="https://kit.fontawesome.com/d9092f3148.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</html>
