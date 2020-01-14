<?php

namespace alat\common;

abstract class ComparisonOperator {
   const lt = 0;
   const lte = 1;
   const eq = 2;
   const gte = 3;
   const gt = 4;
   const contains = 5;

   public static function Compare($operator, $value1, $value2) {
      if ($operator == ComparisonOperator::lt) {
         return $value1 < $value2;
      } else if ($operator == ComparisonOperator::lte) {
         return $value1 <= $value2;
      } else if ($operator == ComparisonOperator::eq) {
         return $value1 == $value2;
      } else if ($operator == ComparisonOperator::gte) {
         return $value1 >= $value2;
      } else if ($operator == ComparisonOperator::gt) {
         return $value1 > $value2;
      } else if ($operator == ComparisonOperator::contains) {
         return strpos($value2, $value1) !== false;
      }
   }
}