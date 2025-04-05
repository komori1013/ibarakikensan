<?php
namespace ibarakikensan\common;

class Date
{
  public static function getDate(){
    $yearArr = [];
    $monthArr = [];
    $dayArr = [];

    $next_year = date("Y") + 1;

    for($i = 1900; $i < $next_year; $i++){
      $year = sprintf("%04d", $i);
      $yearArr[$year] = $year;
    }

    for ($i = 1; $i < 13; $i ++) {
      $month = sprintf("%02d", $i);
      $monthArr[$month] = $month. "月";
    }

    for ($i = 1; $i < 32; $i ++) {
      $day = sprintf("%02d", $i);
      $dayArr[$day] = $day. "日";
    }
    
    return [$yearArr, $monthArr, $dayArr];
  }
  
}
