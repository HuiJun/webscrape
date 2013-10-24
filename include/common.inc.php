<?php

function getmonth ($month = null, $year = null)
{
      if (is_null($month))
          $month = date('n');

      if (is_null($year))
          $year = date('Y');

      if (!checkdate($month, 1, $year))
          return null;

      $first_of_month = mktime(0, 0, 0, $month, 1, $year);
      $days_in_month = date('t', $first_of_month);
      $last_of_month = mktime(0, 0, 0, $month, $days_in_month, $year);

      $m = array();
      $m['first_mday'] = 1;
      $m['first_wday'] = date('w', $first_of_month);
      $m['first_weekday'] = strftime('%A', $first_of_month);
      $m['first_yday'] = date('z', $first_of_month);
      $m['first_week'] = date('W', $first_of_month);
      $m['last_mday'] = $days_in_month;
      $m['last_wday'] = date('w', $last_of_month);
      $m['last_weekday'] = strftime('%A', $last_of_month);
      $m['last_yday'] = date('z', $last_of_month);
      $m['last_week'] = date('W', $last_of_month);
      $m['mon'] = $month;
      $m['month'] = strftime('%B', $first_of_month);
      $m['year'] = $year;

      return $m;
}

function getisomonday($year, $week)
{
      $year = min ($year, 2038); $year = max ($year, 1970);
      $week = min ($week, 53); $week = max ($week, 1);
      $monday = mktime (1,1,1,1,7*$week,$year);
      while (strftime('%V', $monday) != $week)
                $monday -= 60*60*24*7;
        while (strftime('%u', $monday) != 1)
                $monday -= 60*60*24;
        return $monday;
}