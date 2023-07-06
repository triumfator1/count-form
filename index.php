<?php
require_once 'backend/sdbh.php';
require_once 'backend/CountForm.php';
$dbh = new sdbh();

?>
<html>
    <head>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
      <link href="assets/css/style.css" rel="stylesheet" />
      <link href="style_form.min.css" rel="stylesheet" />
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
      <style>
              .container{
                  margin-top: 50px;
                  border-radius: 15px;
                  border: 3px solid #333;
              }
              .col-3{
                  background-color: #FF9A00;
                  border-radius: 0;
                  border-top-left-radius: 13px;
                  border-bottom-left-radius: 13px;
                  display: flex;
                  align-items: center;
                  flex-flow: column;
                  justify-content: center;
                  font-size: 26px;
                  font-weight: 900;
              }
              label:not([class="form-check-label"]) {
                  font-size: 16px;
                  font-weight: 600;
              }
              .form-check-input:checked{
                  background-color: #FF9A00;
                  border-color: #FF9A00;
              }
              .col-9{
                  padding: 25px;
              }
              .btn-primary {
                  color: #fff;
                  background-color: #FF9A00;
                  border-color: #FF9A00;
              }
      </style>
    </head>
    <body>
      <div class="container">
        <div class="row row-header">
            <div class="col-12">
                <img src="assets/img/logo.png" alt="logo" style="max-height:50px"/>
                <h1>Прокат</h1>                    
            </div>
        </div>
      </div>
      <div class="container">
          <div class="row row-body">
            <div class="col-3">
                    <span style="text-align: center">Форма обратной связи</span>
                    <i class="bi bi-activity"></i>
                </div>
            <div class="col-9">
                    <form action="" id="form">
                      <?php $products = $dbh->mselect_rows(
                          'a25_products',
                          null,
                          0,
                          10,
                          'id'
                        );
                      ?>
                            <label class="form-label" for="product">Выберите продукт:</label>
                            <select class="form-select" name="product" id="product">
                              <?php foreach ($products as $product): ?>
                                <option
                                  value="<?php echo $product['PRICE'] ?>"
                                  <?php echo isset($_GET['product']) && ($_GET['product'] == $product['PRICE']) ? 'selected' : '' ?>
                                >
                                  <?php echo $product['NAME']?> за <?php echo $product['PRICE'] ?>                                
                                </option>
                              <?php endforeach; ?>
                            </select>

                            <label for="days" class="form-label">Количество дней:</label>
                            <input name="days" type="text" class="form-control" id="days" min="1" max="30" value="<?php echo $_GET['days'] ?? '' ?>">

                            <label for="customRange1" class="form-label">Дополнительно:</label>
                            

                            <?php
                              $services = unserialize(
                                $dbh->mselect_rows(
                                  'a25_settings',
                                  ['set_key' => 'services'],
                                  0,
                                  1,
                                  'id'
                                )[0]['set_value']
                              );
                              $i = 0;
                            foreach ($services as $service => $value): ?>
                              <div class="form-check">
                                  <input name="service_<?= $i?>" class="form-check-input" type="checkbox" value="<?php echo $value ?>" id="flexCheckChecked1" <?php echo isset($_GET['service_' . $i]) ? 'checked' : '' ?>>
                                  <label class="form-check-label" for="flexCheckChecked1">
                                      <?php echo $service . ' за ' . $value ?>
                                  </label>
                              </div>                                                          
                            <?php 
                          $i++;
                          endforeach; ?>
                            <button type="submit" class="btn btn-primary">Рассчитать</button>
                            <?php
                              if (!empty($_GET)) {
                                $form = new CountForm();
                                echo $form->getTotalCost();
                              }
                            ?>
                    </form>
                </div>
          </div>
      </div>
    </body>
</html>
