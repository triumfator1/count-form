<?php

require_once 'backend/sdbh.php';

class CountForm {
  private sdbh $db;
  private int $price;
  private int $days;
  private array $services = [];
  private array $tariffs = [];

  public function __construct()
  {
    $this->db = new sdbh;
    $this->price = (int)($_GET['product'] ?? 0);
    $this->days = (int)($_GET['days'] ?? 0);

    for ($i = 0; $i < 4; $i++) {
      if (!empty($_GET['service_' . $i])) {
        $this->services['service_' . $i] = (int)$_GET['service_' . $i];
      }
    }    

    $tariffField = $this->db->getTariffField($this->price);    
    if (!empty($tariffField)) {
      $i = 0;
      foreach ($tariffField as $period => $price) {
        $this->tariffs[$i] = [
          'period' => $period,
          'price' => $price
        ];
        $i++;
      }
    }
  }

  public function getTotalCost() : int|string
  {    
    if ($errors = $this->validate()) {
      return $errors;
    }

    return $this->getProductCost() + $this->getServicesCost();
  }

  private function validate() :string
  {
    $errors = '';
    if (!$this->price || $this->price < 0) {
      $errors = "Error: no or wrong price value set.\n";
    }
    if (!$this->days || $this->days < 0) {
      $errors .= 'Error: no or wrong days value set.';
    }
    return $errors;
  }

  private function getProductCost() : int
  {    
    $count = count($this->tariffs);
    if (!$count) {
      return $this->price * $this->days;
    }

    /* subtract 0 day */
    $result = -$this->tariffs[0]['price'];

    $previousTariff = null;
    $i = 0;
    foreach ($this->tariffs as $currentTariff) {
      if ($this->days == $currentTariff['period']) {
        $result+= ($currentTariff['period'] - $previousTariff['period'])*$previousTariff['price'];        
        return $result+= $currentTariff['price'];
      }

      if ($this->days < $currentTariff['period']) {
        return $result+= ($this->days-$previousTariff['period'])*$previousTariff['price'];
      }

      if ($this->days > $currentTariff['period'] && $currentTariff['period']) {
        $result+= ($currentTariff['period'] - $previousTariff['period'])*$previousTariff['price'];
        if ($i == $count - 1) {
          return $result += ($this->days - $currentTariff['period'])*$currentTariff['price'];
        }
      }

      $i++;
      $previousTariff = $currentTariff;
    }
  }

  private function getServicesCost() : int
  {
    $result = 0;
    if (count($this->services)) {
      $result = array_reduce($this->services, fn ($summ, $servicePrice) =>
        $summ += $servicePrice*$this->days
      , 0);
    }
    return $result;
  }  
}
