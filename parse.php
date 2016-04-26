<?php

class CSVParse
{

  private $handle;

  public function __construct($handle)
  {
    $this->handle = $handle;
  }

  public function in_array_r($needle, $haystack)
  {
    foreach ($haystack as $item) {
        if($item['sku'] == $needle) {
          return true;
        }
    }

    return false;
  }

  public function DataToArray($delimiter=',')
  {
    $handle = $this->handle;

  	if(!file_exists($handle) || !is_readable($handle))
  		return false;

  	$header = NULL;
  	$data = array();
  	if (($handle = fopen($handle, 'r')) !== FALSE)
  	{
  		while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
  		{
  			if(!$header)
  				$header = $row;
  			else
  				$data[] = array_combine($header, $row);
  		}
  		fclose($handle);
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

      if(substr($row['SKU'], 0, strpos($row['SKU'], '-'))) {
        $sku = substr($row['SKU'], 0, strpos($row['SKU'], '-'));
      } else {
        $sku = $row['SKU'];
      }

    	if(!$this->in_array_r($sku, $data)) {
    		$data[] =
    						array(
    							'sku' =>  $sku,
    							'name' => $row['Name'],
                  'configurable' => NULL
    						);

    	}

    }

    foreach ($data as $item => $value) {

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
    var_dump("<pre>",$data,"</pre>");
    //return $data;
  }

  public function DataToCsv()
  {
    $data = array();

    $rows = $this->DataToArray();
    foreach($rows as $row)
    {
      $sku = substr($row['SKU'], 0, strpos($row['SKU'], '-'));

    	if(!$this->in_array_r($sku, $data)) {
    		$data[] =
    						array(
    							'sku' => strtolower(trim($sku)),
    							'name' => $row['Name'],
                  'configurable' => $row['configurable']
    						);

    	}

    }
    return $data;
  }
}


		//fputcsv($out, array($customer['name'],$customer['email'],number_format($money_spent, 2),'1'));


  $csv = new CSVParse("test.csv");

  $csv->CreateConfigurable();

?>
