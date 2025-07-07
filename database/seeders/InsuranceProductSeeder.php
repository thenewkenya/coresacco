<?php

namespace Database\Seeders;

use App\Models\InsuranceProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InsuranceProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Life Insurance Products
            [
                'name' => 'Term Life Protection',
                'description' => 'Affordable term life insurance providing financial security for your family with flexible coverage periods.',
                'insurance_type' => 'life',
                'coverage_type' => 'term_life',
                'min_coverage_amount' => 100000,
                'max_coverage_amount' => 5000000,
                'min_age' => 18,
                'max_age' => 65,
                'base_premium_rate' => 0.45, // 0.45% of coverage amount annually
                'risk_factors' => [
                    'age' => [
                        '18-30' => 1.0,
                        '31-40' => 1.2,
                        '41-50' => 1.5,
                        '51-65' => 2.0
                    ],
                    'smoking' => [
                        'non_smoker' => 1.0,
                        'smoker' => 1.8
                    ],
                    'health' => [
                        'excellent' => 1.0,
                        'good' => 1.1,
                        'fair' => 1.3,
                        'poor' => 2.0
                    ]
                ],
                'coverage_benefits' => [
                    'death_benefit' => 'Full coverage amount',
                    'terminal_illness' => 'Up to 50% advance payment',
                    'accidental_death' => 'Double coverage for accidental death',
                    'premium_waiver' => 'Premium waiver in case of disability'
                ],
                'exclusions' => [
                    'suicide' => 'No coverage for suicide within first 2 years',
                    'war' => 'No coverage for death during war or military action',
                    'criminal_activity' => 'No coverage during criminal activities',
                    'misrepresentation' => 'Policy void if material facts misrepresented'
                ],
                'terms_conditions' => [
                    'grace_period' => '30 days after premium due date',
                    'contestability' => '2 years from policy start date',
                    'beneficiary_changes' => 'Allowed anytime with proper documentation',
                    'conversion_option' => 'Can convert to whole life before age 65'
                ],
                'premium_frequency' => 'monthly',
                'grace_period_days' => 30,
                'waiting_period_days' => 0,
                'claim_settlement_days' => 30,
                'renewal_terms' => [
                    'automatic_renewal' => true,
                    'rate_review' => 'Every 5 years',
                    'maximum_renewal_age' => 75
                ],
                'status' => 'active',
                'requires_medical_exam' => true,
                'requires_property_inspection' => false,
                'commission_rate' => 5.0,
                'metadata' => [
                    'underwriting_class' => 'standard',
                    'min_sum_assured' => 100000,
                    'max_sum_assured' => 5000000
                ]
            ],
            
            [
                'name' => 'Whole Life Wealth Builder',
                'description' => 'Permanent life insurance with cash value accumulation and guaranteed returns.',
                'insurance_type' => 'life',
                'coverage_type' => 'whole_life',
                'min_coverage_amount' => 250000,
                'max_coverage_amount' => 10000000,
                'min_age' => 18,
                'max_age' => 55,
                'base_premium_rate' => 1.2, // Higher premium for cash value component
                'risk_factors' => [
                    'age' => [
                        '18-30' => 1.0,
                        '31-40' => 1.3,
                        '41-55' => 1.8
                    ],
                    'health' => [
                        'excellent' => 1.0,
                        'good' => 1.2,
                        'fair' => 1.5
                    ]
                ],
                'coverage_benefits' => [
                    'death_benefit' => 'Guaranteed death benefit plus cash value',
                    'cash_value' => 'Guaranteed cash value accumulation',
                    'loan_facility' => 'Borrow against cash value up to 90%',
                    'paid_up_option' => 'Convert to paid-up policy after 10 years'
                ],
                'exclusions' => [
                    'suicide' => 'No coverage for suicide within first 2 years',
                    'misrepresentation' => 'Policy void if material facts misrepresented'
                ],
                'terms_conditions' => [
                    'cash_value_guarantee' => 'Minimum 3% annual return',
                    'surrender_charges' => 'Applicable for first 15 years',
                    'dividend_participation' => 'Eligible for annual dividends'
                ],
                'premium_frequency' => 'annually',
                'grace_period_days' => 31,
                'waiting_period_days' => 0,
                'claim_settlement_days' => 30,
                'status' => 'active',
                'requires_medical_exam' => true,
                'requires_property_inspection' => false,
                'commission_rate' => 7.5
            ],

            // Health Insurance Products
            [
                'name' => 'Comprehensive Health Shield',
                'description' => 'Complete health insurance covering hospitalization, outpatient care, and emergency services.',
                'insurance_type' => 'health',
                'coverage_type' => 'medical',
                'min_coverage_amount' => 50000,
                'max_coverage_amount' => 2000000,
                'min_age' => 18,
                'max_age' => 75,
                'base_premium_rate' => 3.5, // 3.5% of coverage amount annually
                'risk_factors' => [
                    'age' => [
                        '18-30' => 1.0,
                        '31-45' => 1.4,
                        '46-60' => 2.0,
                        '61-75' => 3.5
                    ],
                    'pre_existing' => [
                        'none' => 1.0,
                        'diabetes' => 1.8,
                        'hypertension' => 1.5,
                        'heart_disease' => 2.5
                    ]
                ],
                'coverage_benefits' => [
                    'hospitalization' => 'Full coverage for room and board',
                    'surgery' => 'All surgical procedures covered',
                    'emergency' => '24/7 emergency room coverage',
                    'outpatient' => 'Specialist consultations and diagnostics',
                    'pharmacy' => 'Prescription medications',
                    'maternity' => 'Maternity and childbirth coverage'
                ],
                'exclusions' => [
                    'cosmetic' => 'Cosmetic and elective procedures',
                    'experimental' => 'Experimental treatments',
                    'pre_existing' => 'Pre-existing conditions for first 12 months',
                    'self_inflicted' => 'Self-inflicted injuries'
                ],
                'premium_frequency' => 'monthly',
                'grace_period_days' => 30,
                'waiting_period_days' => 90,
                'claim_settlement_days' => 15,
                'status' => 'active',
                'requires_medical_exam' => true,
                'commission_rate' => 12.0
            ],

            [
                'name' => 'Family Dental Care Plus',
                'description' => 'Comprehensive dental insurance covering preventive, basic, and major dental procedures.',
                'insurance_type' => 'health',
                'coverage_type' => 'dental',
                'min_coverage_amount' => 5000,
                'max_coverage_amount' => 50000,
                'min_age' => 1,
                'max_age' => 80,
                'base_premium_rate' => 8.0, // Higher rate for dental
                'coverage_benefits' => [
                    'preventive' => '100% coverage for cleanings and exams',
                    'basic' => '80% coverage for fillings and extractions',
                    'major' => '50% coverage for crowns and bridges',
                    'orthodontics' => '50% coverage up to lifetime maximum'
                ],
                'premium_frequency' => 'monthly',
                'grace_period_days' => 30,
                'waiting_period_days' => 180,
                'claim_settlement_days' => 10,
                'status' => 'active',
                'requires_medical_exam' => false,
                'commission_rate' => 15.0
            ],

            // Property Insurance Products
            [
                'name' => 'Home Protection Comprehensive',
                'description' => 'Complete home insurance protecting your property and belongings against various risks.',
                'insurance_type' => 'property',
                'coverage_type' => 'home',
                'min_coverage_amount' => 100000,
                'max_coverage_amount' => 20000000,
                'min_age' => 18,
                'max_age' => 85,
                'base_premium_rate' => 0.15, // 0.15% of property value
                'risk_factors' => [
                    'location' => [
                        'low_risk' => 1.0,
                        'medium_risk' => 1.3,
                        'high_risk' => 2.0,
                        'flood_zone' => 2.5
                    ],
                    'construction' => [
                        'brick' => 1.0,
                        'wood' => 1.4,
                        'mixed' => 1.2
                    ],
                    'security' => [
                        'high_security' => 0.9,
                        'basic_security' => 1.0,
                        'no_security' => 1.3
                    ]
                ],
                'coverage_benefits' => [
                    'structure' => 'Full replacement cost of dwelling',
                    'contents' => 'Personal property coverage',
                    'liability' => 'Personal liability protection',
                    'additional_living' => 'Temporary living expenses',
                    'detached_structures' => 'Coverage for garages, sheds'
                ],
                'exclusions' => [
                    'flood' => 'Flood damage requires separate coverage',
                    'earthquake' => 'Earthquake damage excluded',
                    'wear_tear' => 'Normal wear and tear',
                    'intentional' => 'Intentional damage by insured'
                ],
                'premium_frequency' => 'annually',
                'grace_period_days' => 30,
                'waiting_period_days' => 0,
                'claim_settlement_days' => 30,
                'status' => 'active',
                'requires_property_inspection' => true,
                'commission_rate' => 10.0
            ],

            [
                'name' => 'Auto Protection Complete',
                'description' => 'Comprehensive auto insurance covering collision, liability, and comprehensive damages.',
                'insurance_type' => 'property',
                'coverage_type' => 'auto',
                'min_coverage_amount' => 25000,
                'max_coverage_amount' => 2000000,
                'min_age' => 18,
                'max_age' => 80,
                'base_premium_rate' => 2.5,
                'risk_factors' => [
                    'driver_age' => [
                        '18-25' => 1.8,
                        '26-55' => 1.0,
                        '56-80' => 1.3
                    ],
                    'driving_record' => [
                        'clean' => 1.0,
                        'minor_violations' => 1.4,
                        'major_violations' => 2.2
                    ],
                    'vehicle_type' => [
                        'sedan' => 1.0,
                        'suv' => 1.2,
                        'sports_car' => 1.8,
                        'commercial' => 2.0
                    ]
                ],
                'coverage_benefits' => [
                    'liability' => 'Bodily injury and property damage',
                    'collision' => 'Damage from collisions',
                    'comprehensive' => 'Theft, vandalism, weather damage',
                    'uninsured_motorist' => 'Protection against uninsured drivers',
                    'medical_payments' => 'Medical expenses for passengers'
                ],
                'premium_frequency' => 'monthly',
                'grace_period_days' => 15,
                'waiting_period_days' => 0,
                'claim_settlement_days' => 20,
                'status' => 'active',
                'requires_property_inspection' => true,
                'commission_rate' => 12.0
            ],

            // Crop Insurance Products
            [
                'name' => 'Multi-Peril Crop Protection',
                'description' => 'Comprehensive crop insurance protecting against yield losses due to natural disasters and weather.',
                'insurance_type' => 'crop',
                'coverage_type' => 'crop_yield',
                'min_coverage_amount' => 10000,
                'max_coverage_amount' => 5000000,
                'min_age' => 18,
                'max_age' => 75,
                'base_premium_rate' => 5.5,
                'risk_factors' => [
                    'crop_type' => [
                        'grains' => 1.0,
                        'fruits' => 1.3,
                        'vegetables' => 1.5,
                        'cash_crops' => 1.8
                    ],
                    'region' => [
                        'low_risk' => 1.0,
                        'medium_risk' => 1.4,
                        'high_risk' => 2.0
                    ],
                    'irrigation' => [
                        'irrigated' => 1.0,
                        'rain_fed' => 1.5
                    ]
                ],
                'coverage_benefits' => [
                    'yield_loss' => 'Protection against yield reduction',
                    'quality_loss' => 'Coverage for quality deterioration',
                    'replanting' => 'Replanting costs coverage',
                    'prevented_planting' => 'Coverage when unable to plant'
                ],
                'exclusions' => [
                    'poor_farming' => 'Losses due to poor farming practices',
                    'war' => 'War and civil commotion',
                    'nuclear' => 'Nuclear contamination'
                ],
                'premium_frequency' => 'annually',
                'grace_period_days' => 30,
                'waiting_period_days' => 30,
                'claim_settlement_days' => 45,
                'status' => 'active',
                'requires_property_inspection' => true,
                'commission_rate' => 8.0
            ],

            // Microinsurance Products
            [
                'name' => 'Micro Life Protector',
                'description' => 'Affordable life insurance designed for low-income families with simplified application process.',
                'insurance_type' => 'micro',
                'coverage_type' => 'micro_life',
                'min_coverage_amount' => 5000,
                'max_coverage_amount' => 100000,
                'min_age' => 18,
                'max_age' => 60,
                'base_premium_rate' => 2.0,
                'coverage_benefits' => [
                    'natural_death' => 'Full coverage amount',
                    'accidental_death' => 'Double coverage amount',
                    'funeral_expenses' => 'Additional funeral expense coverage'
                ],
                'exclusions' => [
                    'suicide' => 'No coverage for suicide within first year',
                    'pre_existing' => 'Pre-existing terminal illness'
                ],
                'premium_frequency' => 'monthly',
                'grace_period_days' => 30,
                'waiting_period_days' => 0,
                'claim_settlement_days' => 15,
                'status' => 'active',
                'requires_medical_exam' => false,
                'commission_rate' => 20.0
            ],

            [
                'name' => 'Micro Health Basic',
                'description' => 'Basic health insurance providing essential medical coverage for low-income individuals.',
                'insurance_type' => 'micro',
                'coverage_type' => 'micro_health',
                'min_coverage_amount' => 2000,
                'max_coverage_amount' => 25000,
                'min_age' => 1,
                'max_age' => 70,
                'base_premium_rate' => 12.0,
                'coverage_benefits' => [
                    'hospitalization' => 'Basic hospitalization coverage',
                    'emergency' => 'Emergency medical treatment',
                    'maternity' => 'Basic maternity coverage'
                ],
                'premium_frequency' => 'monthly',
                'grace_period_days' => 30,
                'waiting_period_days' => 30,
                'claim_settlement_days' => 10,
                'status' => 'active',
                'requires_medical_exam' => false,
                'commission_rate' => 25.0
            ]
        ];

        foreach ($products as $product) {
            InsuranceProduct::create($product);
        }
    }
}
