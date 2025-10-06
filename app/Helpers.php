<?php
if (!function_exists('money')) {
  function money(int $cents, string $symbol='€'): string {
    return number_format($cents/100, 2, ',', '.') . " $symbol";
  }
}