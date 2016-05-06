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

      if(substr($row['sku'], 0, strpos($row['sku'], '-')))
      {
        $sku = substr($row['sku'], 0, strpos($row['sku'], '-'));
      }
      else
      {
        $sku = $row['sku'];
      }

      if($row['short_description'])
      {
        $short = $row['short_description'];
      }
      else {
        $short = $row['description'];
      }

      if(!$this->in_array_r($sku, $data))
      {
        $data[] =
                array(
                  'sku'               => $sku,
                  'name'              => $row['name'],
                  'category_ids'      => $row['category'],
                  'description'       => $row['description'],
                  'short_description' => $short,
                  'color'             => $row['color'],
                  'price'             => $row['price'],
                  'configurable'      => NULL
                );
      }

    }

    foreach ($data as $item => $value)
    {
      $i = 0;
      foreach ($rows as $row) {
        if(strpos($row['sku'], $data[$item]['sku']) !== FALSE)
        {
          $del = ($i == 0) ? "" : ",";
          $data[$item]['configurable'] .= $del.strtoupper($row['sku']);
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
		  fputcsv($new, array($row['sku'],$row['name'],$row['configurable'],$row['category_ids'],$row['color'],$row['description'],$row['short_description'],$row['price']));
    }
    fclose($new);

    echo "CSV Created";
  }
}

  $csv = new CSVParse("frenchie-spring-simple-import.csv");

  $csv->CreateCSV();

?>
