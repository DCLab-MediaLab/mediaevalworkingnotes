<?php
$files=array(
"me14sh_DCLab_S_1_Sh_M_N_ConceptEnrichment.eval" => "Manual subtitles",
"me14sh_DCLab_S_2_Sh_I_N_ConceptEnrichment.eval" => "LIMSI transcripts",
"me14sh_DCLab_S_3_Sh_U_N_ConceptEnrichment.eval" => "LIUM transcripts", 
"me14sh_DCLab_S_4_Sh_S_N_ConceptEnrichment.eval" => "NST/Sheffield",
"me14sh_DCLab_S_5_Sh_IMSU_N_ConceptEnrichment.eval" => "All transcript and subtitle",
"me14sh_DCLab_S_6_Sh_M_N_Concept2.eval" => "Manual subtitles (Concept2)",
"me14sh_DCLab_S_7_Sh_I_N_Concept2.eval" => "LIMSI transcripts (Concept2)",
"me14sh_DCLab_S_8_Sh_U_N_Concept2.eval" => "LIUM transcripts (Concept2)",
"me14sh_DCLab_S_9_Sh_S_N_Concept2.eval" => "NST/Sheffield (Concept2)",
"me14sh_DCLab_S_9_Sh_IMSU_N_Concept2.eval" => "All transcript and subtitle (Concept2)"
);

/*
 * Will find "data" lines in files, collects them into one array (key is anchor)
 */
function findlines($is_search,$file,$data)
{
  $ret=array();
  
  if ($is_search)
  {
    $file="searching/".$file;
  }else{
    $file="linking/".str_replace("DCLab_S_","DCLab_L_",$file);
  }
  
  $lines=explode("\n",file_get_contents($file));
  foreach($lines as $l1){
  
    for ($i=0;$i<20;$i++)
    {
      $l1=str_replace(array("   ","  ","\t")," ",$l1);
    }
    $l1=explode(" ",$l1);
    
    if (count($l1)<3)
      continue;
      
      if ($l1[0]!=$data)
      continue;
      
      $ret[$l1[1]]=$l1[2];
  }
  return $ret;
}

/*
 * Do statistics calculation for the whole dataset
 */
function doStat($is_search,$data)
{
global $files;

  $ret=array();
  
  foreach($files as $fname => $fresult)
  {
    $ret[$fresult]=findlines($is_search,$fname,$data);
  }
  
  return $ret;
}


/*
 * save to csv
 */
function dropToCsv($filename,$data)
{
  $ret="";
  $first=true;
  $header=array();
  
  foreach($data as $name => $d1){
    if ($first)
    {
      $ret.="name;".implode(";",array_keys($d1))."\n";
      $header=$d1;
      $first=false;
    }
    
    $ret.=$name;
    foreach($header as $key => $noneed){
        if (isset($d1[$key]))
        {
          $ret.=";".$d1[$key];
        }else{
          $ret.=";0";
        }
    }
    $ret.="\n";    
  }
  
  file_put_contents($filename,$ret);
}

dropToCsv("search_P_5.csv",doStat(true,"P_5"));
dropToCsv("search_P_10.csv",doStat(true,"P_10"));
dropToCsv("search_P_20.csv",doStat(true,"P_20"));

dropToCsv("linking_P_5.csv",doStat(false,"P_5"));
dropToCsv("linking_P_10.csv",doStat(false,"P_10"));
dropToCsv("linking_P_20.csv",doStat(false,"P_20"));
?>