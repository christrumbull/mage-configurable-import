<?php

class CSVParse
{

  public function __construct($handle, $del = ',', $newfile = 'new.csv')
  {
    $this->handle = $handle;
    $this->del    = $del;
    $this->new    = uniqid()."-".$newfile;
  }

  public function in_array_r($needle, $haystack)
  {

    foreach ($haystack as $item)
    {
      if($item['sku'] == $needle) {
        return true;
      }
    }
    return false;
  }

  public function DataToArray()
  {

    if(!file_exists($this->handle) || !is_readable($this->handle))
      return false;

    $header = NULL;
    $data = array();
    if (($this->handle = fopen($this->handle, 'r')) !== FALSE)
    {
      while (($row = fgetcsv($this->handle, 1000, $this->del)) !== FALSE)
      {
        if(!$header)
          $header = $row;
        else
          $data[] = array_combine($header, $row);
        }
    fclose($this->handle);
    }
    return $data;
  }

  public function CreateConfigurable()
  {

    $rows = $this->DataToArray();
    $data = array();
    $counts = array();
    foreach($rows as $row)
    {

      if(substr($row['SKU'], 0, strpos($row['SKU'], '-')))
      {
        $sku = substr($row['SKU'], 0, strpos($row['SKU'], '-'));
      } else {
        $sku = $row['SKU'];
      }

      if(!$this->in_array_r($sku, $data))
      {
        $data[] =
                array(
                  'sku' =>  $sku,
                  'name' => $row['Name'],
                  'configurable' => NULL
                );
      }

    }

    foreach ($data as $item => $value)
    {
      $i = 0;
      foreach ($rows as $row) {
        if(strpos($row['SKU'], $data[$item]['sku']) !== FALSE)
        {
          $del = ($i == 0) ? "" : ",";
          $data[$item]['configurable'] .= $del.strtoupper($row['SKU']);
          $i++;
        }
      }
    }
    return $data;
  }

  public function CreateCSV()
  {
    $new = fopen($this->new, "a");
    $rows = $this->CreateConfigurable();
    foreach ($rows as $row)
    {
		  fputcsv($new, array($row['sku'],$row['name'],$row['configurable']));
    }
    fclose($new);

    echo "CSV Created";
  }
}

  $csv = new CSVParse("test.csv");

  $csv->CreateCSV();

?>
