<?php

namespace Database\Seeders;

use App\Models\DeductionType;
use Illuminate\Database\Seeder;

class DeductionTypeSeeder extends Seeder
{
    public function run(): void
    {
        // SSS (2026 brackets)
        DeductionType::create([
            'name' => 'SSS',
            'code' => 'SSS',
            'description' => 'Social Security System',
            'computationMethod' => 'bracket',
            'isStatutory' => true,
            'isActive' => true,
            'employeeRate' => 0.0450,
            'employerRate' => 0.0950,
            'bracketTable' => [
                ['min' => 0, 'max' => 4249.99, 'msc' => 4000, 'employeeShare' => 180.00, 'employerShare' => 380.00, 'ec' => 10.00],
                ['min' => 4250, 'max' => 4749.99, 'msc' => 4500, 'employeeShare' => 202.50, 'employerShare' => 427.50, 'ec' => 10.00],
                ['min' => 4750, 'max' => 5249.99, 'msc' => 5000, 'employeeShare' => 225.00, 'employerShare' => 475.00, 'ec' => 10.00],
                ['min' => 5250, 'max' => 5749.99, 'msc' => 5500, 'employeeShare' => 247.50, 'employerShare' => 522.50, 'ec' => 10.00],
                ['min' => 5750, 'max' => 6249.99, 'msc' => 6000, 'employeeShare' => 270.00, 'employerShare' => 570.00, 'ec' => 10.00],
                ['min' => 6250, 'max' => 6749.99, 'msc' => 6500, 'employeeShare' => 292.50, 'employerShare' => 617.50, 'ec' => 10.00],
                ['min' => 6750, 'max' => 7249.99, 'msc' => 7000, 'employeeShare' => 315.00, 'employerShare' => 665.00, 'ec' => 10.00],
                ['min' => 7250, 'max' => 7749.99, 'msc' => 7500, 'employeeShare' => 337.50, 'employerShare' => 712.50, 'ec' => 10.00],
                ['min' => 7750, 'max' => 8249.99, 'msc' => 8000, 'employeeShare' => 360.00, 'employerShare' => 760.00, 'ec' => 10.00],
                ['min' => 8250, 'max' => 8749.99, 'msc' => 8500, 'employeeShare' => 382.50, 'employerShare' => 807.50, 'ec' => 10.00],
                ['min' => 8750, 'max' => 9249.99, 'msc' => 9000, 'employeeShare' => 405.00, 'employerShare' => 855.00, 'ec' => 10.00],
                ['min' => 9250, 'max' => 9749.99, 'msc' => 9500, 'employeeShare' => 427.50, 'employerShare' => 902.50, 'ec' => 10.00],
                ['min' => 9750, 'max' => 10249.99, 'msc' => 10000, 'employeeShare' => 450.00, 'employerShare' => 950.00, 'ec' => 10.00],
                ['min' => 10250, 'max' => 10749.99, 'msc' => 10500, 'employeeShare' => 472.50, 'employerShare' => 997.50, 'ec' => 10.00],
                ['min' => 10750, 'max' => 11249.99, 'msc' => 11000, 'employeeShare' => 495.00, 'employerShare' => 1045.00, 'ec' => 10.00],
                ['min' => 11250, 'max' => 11749.99, 'msc' => 11500, 'employeeShare' => 517.50, 'employerShare' => 1092.50, 'ec' => 10.00],
                ['min' => 11750, 'max' => 12249.99, 'msc' => 12000, 'employeeShare' => 540.00, 'employerShare' => 1140.00, 'ec' => 10.00],
                ['min' => 12250, 'max' => 12749.99, 'msc' => 12500, 'employeeShare' => 562.50, 'employerShare' => 1187.50, 'ec' => 10.00],
                ['min' => 12750, 'max' => 13249.99, 'msc' => 13000, 'employeeShare' => 585.00, 'employerShare' => 1235.00, 'ec' => 10.00],
                ['min' => 13250, 'max' => 13749.99, 'msc' => 13500, 'employeeShare' => 607.50, 'employerShare' => 1282.50, 'ec' => 10.00],
                ['min' => 13750, 'max' => 14249.99, 'msc' => 14000, 'employeeShare' => 630.00, 'employerShare' => 1330.00, 'ec' => 10.00],
                ['min' => 14250, 'max' => 14749.99, 'msc' => 14500, 'employeeShare' => 652.50, 'employerShare' => 1377.50, 'ec' => 10.00],
                ['min' => 14750, 'max' => 15249.99, 'msc' => 15000, 'employeeShare' => 675.00, 'employerShare' => 1425.00, 'ec' => 10.00],
                ['min' => 15250, 'max' => 15749.99, 'msc' => 15500, 'employeeShare' => 697.50, 'employerShare' => 1472.50, 'ec' => 30.00],
                ['min' => 15750, 'max' => 16249.99, 'msc' => 16000, 'employeeShare' => 720.00, 'employerShare' => 1520.00, 'ec' => 30.00],
                ['min' => 16250, 'max' => 16749.99, 'msc' => 16500, 'employeeShare' => 742.50, 'employerShare' => 1567.50, 'ec' => 30.00],
                ['min' => 16750, 'max' => 17249.99, 'msc' => 17000, 'employeeShare' => 765.00, 'employerShare' => 1615.00, 'ec' => 30.00],
                ['min' => 17250, 'max' => 17749.99, 'msc' => 17500, 'employeeShare' => 787.50, 'employerShare' => 1662.50, 'ec' => 30.00],
                ['min' => 17750, 'max' => 18249.99, 'msc' => 18000, 'employeeShare' => 810.00, 'employerShare' => 1710.00, 'ec' => 30.00],
                ['min' => 18250, 'max' => 18749.99, 'msc' => 18500, 'employeeShare' => 832.50, 'employerShare' => 1757.50, 'ec' => 30.00],
                ['min' => 18750, 'max' => 19249.99, 'msc' => 19000, 'employeeShare' => 855.00, 'employerShare' => 1805.00, 'ec' => 30.00],
                ['min' => 19250, 'max' => 19749.99, 'msc' => 19500, 'employeeShare' => 877.50, 'employerShare' => 1852.50, 'ec' => 30.00],
                ['min' => 19750, 'max' => 20249.99, 'msc' => 20000, 'employeeShare' => 900.00, 'employerShare' => 1900.00, 'ec' => 30.00],
                ['min' => 20250, 'max' => 20749.99, 'msc' => 20500, 'employeeShare' => 922.50, 'employerShare' => 1947.50, 'ec' => 30.00],
                ['min' => 20750, 'max' => 21249.99, 'msc' => 21000, 'employeeShare' => 945.00, 'employerShare' => 1995.00, 'ec' => 30.00],
                ['min' => 21250, 'max' => 21749.99, 'msc' => 21500, 'employeeShare' => 967.50, 'employerShare' => 2042.50, 'ec' => 30.00],
                ['min' => 21750, 'max' => 22249.99, 'msc' => 22000, 'employeeShare' => 990.00, 'employerShare' => 2090.00, 'ec' => 30.00],
                ['min' => 22250, 'max' => 22749.99, 'msc' => 22500, 'employeeShare' => 1012.50, 'employerShare' => 2137.50, 'ec' => 30.00],
                ['min' => 22750, 'max' => 23249.99, 'msc' => 23000, 'employeeShare' => 1035.00, 'employerShare' => 2185.00, 'ec' => 30.00],
                ['min' => 23250, 'max' => 23749.99, 'msc' => 23500, 'employeeShare' => 1057.50, 'employerShare' => 2232.50, 'ec' => 30.00],
                ['min' => 23750, 'max' => 24249.99, 'msc' => 24000, 'employeeShare' => 1080.00, 'employerShare' => 2280.00, 'ec' => 30.00],
                ['min' => 24250, 'max' => 24749.99, 'msc' => 24500, 'employeeShare' => 1102.50, 'employerShare' => 2327.50, 'ec' => 30.00],
                ['min' => 24750, 'max' => 25249.99, 'msc' => 25000, 'employeeShare' => 1125.00, 'employerShare' => 2375.00, 'ec' => 30.00],
                ['min' => 25250, 'max' => 25749.99, 'msc' => 25500, 'employeeShare' => 1147.50, 'employerShare' => 2422.50, 'ec' => 30.00],
                ['min' => 25750, 'max' => 26249.99, 'msc' => 26000, 'employeeShare' => 1170.00, 'employerShare' => 2470.00, 'ec' => 30.00],
                ['min' => 26250, 'max' => 26749.99, 'msc' => 26500, 'employeeShare' => 1192.50, 'employerShare' => 2517.50, 'ec' => 30.00],
                ['min' => 26750, 'max' => 27249.99, 'msc' => 27000, 'employeeShare' => 1215.00, 'employerShare' => 2565.00, 'ec' => 30.00],
                ['min' => 27250, 'max' => 27749.99, 'msc' => 27500, 'employeeShare' => 1237.50, 'employerShare' => 2612.50, 'ec' => 30.00],
                ['min' => 27750, 'max' => 28249.99, 'msc' => 28000, 'employeeShare' => 1260.00, 'employerShare' => 2660.00, 'ec' => 30.00],
                ['min' => 28250, 'max' => 28749.99, 'msc' => 28500, 'employeeShare' => 1282.50, 'employerShare' => 2707.50, 'ec' => 30.00],
                ['min' => 28750, 'max' => 29249.99, 'msc' => 29000, 'employeeShare' => 1305.00, 'employerShare' => 2755.00, 'ec' => 30.00],
                ['min' => 29250, 'max' => 29749.99, 'msc' => 29500, 'employeeShare' => 1327.50, 'employerShare' => 2802.50, 'ec' => 30.00],
                ['min' => 29750, 'max' => null, 'msc' => 30000, 'employeeShare' => 1350.00, 'employerShare' => 2850.00, 'ec' => 30.00],
            ],
        ]);

        // PhilHealth
        DeductionType::create([
            'name' => 'PhilHealth',
            'code' => 'PHIC',
            'description' => 'Philippine Health Insurance Corporation',
            'computationMethod' => 'percentage',
            'isStatutory' => true,
            'isActive' => true,
            'employeeRate' => 0.0250,
            'employerRate' => 0.0250,
            'salaryFloor' => 10000.00,
            'salaryCeiling' => 100000.00,
        ]);

        // Pag-IBIG
        DeductionType::create([
            'name' => 'Pag-IBIG',
            'code' => 'HDMF',
            'description' => 'Home Development Mutual Fund',
            'computationMethod' => 'bracket',
            'isStatutory' => true,
            'isActive' => true,
            'maxEmployeeAmount' => 200.00,
            'maxEmployerAmount' => 200.00,
            'bracketTable' => [
                ['min' => 0, 'max' => 1500, 'employeeRate' => 0.01, 'employerRate' => 0.02],
                ['min' => 1500.01, 'max' => null, 'employeeRate' => 0.02, 'employerRate' => 0.02],
            ],
        ]);

        // Withholding Tax (BIR TRAIN Law, semi-monthly brackets)
        DeductionType::create([
            'name' => 'Withholding Tax',
            'code' => 'TAX',
            'description' => 'BIR Withholding Tax (TRAIN Law)',
            'computationMethod' => 'bracket',
            'isStatutory' => true,
            'isActive' => true,
            'bracketTable' => [
                ['min' => 0, 'max' => 10417, 'base' => 0, 'rate' => 0],
                ['min' => 10417, 'max' => 16667, 'base' => 0, 'rate' => 0.15],
                ['min' => 16667, 'max' => 33333, 'base' => 937.50, 'rate' => 0.20],
                ['min' => 33333, 'max' => 83333, 'base' => 4270.83, 'rate' => 0.25],
                ['min' => 83333, 'max' => 333333, 'base' => 16770.83, 'rate' => 0.30],
                ['min' => 333333, 'max' => null, 'base' => 91770.83, 'rate' => 0.35],
            ],
        ]);
    }
}
