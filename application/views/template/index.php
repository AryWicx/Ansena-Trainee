<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">  
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
        <title><?= isset($title) ? $title : "" ?> - Ansena Trainee</title>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    </head>
    <body>
      <section class="section-navbar">
        <div class="col-sm-12">
          <nav class="navbar navbar-expand-lg navbar navbar-dark bg-dark">
              <div class="container-fluid navbar-container">
                  <a class="navbar-brand" href="index.html">
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

    </body>

    <script src="https://kit.fontawesome.com/d9092f3148.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</html>
