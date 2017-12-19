<?php
# by kos      
$balansF = sprintf("%.2f", $users->{DEPOSIT} + $users->{CREDIT});
$dayAbonF = sprintf("%.2f", $Dv->{DAY_ABON});

if ($dayAbonF > 0) {
    if ($balansF > 0) {
        if ($balansF > $dayAbonF) {
            $t1 = sprintf("%.2f", int($balansF / $dayAbonF) * $dayAbonF);
            $ostatok = sprintf("%.2f", $balansF - $t1);
            if ($ostatok > 0) {
                $days_to_fees = $days_to_fees + 1;
                $warning = "$_SERVICE_ENDED: $days_to_fees ";
            } else {
                if ($ostatok == 0) {
                    $warning = "$_SERVICE_ENDED: $days_to_fees ";
                } else {
                    $warning = " Error in logic! ";
                }
            }
        } else {
            if ($balansF < $dayAbonF) {
                $days_to_fees = 1;
                $warning = "$_SERVICE_ENDED: $days_to_fees ";
            } else {
                if ($balansF == $dayAbonF) {
                    $days_to_fees = 1;
                    $warning = "$_SERVICE_ENDED: $days_to_fees ";
                } else {
                    $warning = " Error in logic! ";
                }
            }
        }
    }
} else {
    $warning = "Besplatno ";
}
# end by kos   

