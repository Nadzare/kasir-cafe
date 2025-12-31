<?php

use Livewire\Volt\Component;
use App\Models\Transaction;
use Carbon\Carbon;

new class extends Component {
    public function with()
    {
        // Ambil data 7 hari terakhir
        $dates = [];
        $totals = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dates[] = $date->format('d M');
            
            // Sum pendapatan per hari
            $total = Transaction::whereDate('created_at', $date)
                ->where('status', 'paid')
                ->sum('total_amount');
            
            $totals[] = $total;
        }

        return [
            'dates' => $dates,
            'totals' => $totals,
        ];
    }
};
?>

<div>
    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Chart Card -->
    <div class="bg-white rounded-xl lg:rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-6">
        <div class="flex items-center justify-between mb-4 lg:mb-6">
            <div>
                <h3 class="text-base lg:text-lg font-bold text-gray-900">Tren Pendapatan</h3>
                <p class="text-xs lg:text-sm text-gray-500 mt-1">7 Hari Terakhir</p>
            </div>
            <div class="bg-green-50 p-2 lg:p-3 rounded-lg lg:rounded-xl">
                <i class="fa-solid fa-chart-line text-lg lg:text-xl text-[#1a4d2e]"></i>
            </div>
        </div>

        <!-- Chart Container -->
        <div id="revenueChart"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                series: [{
                    name: 'Pendapatan',
                    data: @js($totals)
                }],
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                    colors: ['#1a4d2e']
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.6,
                        opacityTo: 0.1,
                        stops: [0, 90, 100],
                        colorStops: [
                            {
                                offset: 0,
                                color: '#1a4d2e',
                                opacity: 0.6
                            },
                            {
                                offset: 100,
                                color: '#4f8f5e',
                                opacity: 0.1
                            }
                        ]
                    }
                },
                xaxis: {
                    categories: @js($dates),
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px'
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px'
                        },
                        formatter: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                            } else {
                                return 'Rp ' + value;
                            }
                        }
                    }
                },
                grid: {
                    borderColor: '#F3F4F6',
                    strokeDashArray: 4,
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    },
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 10
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    },
                    style: {
                        fontSize: '12px'
                    }
                },
                markers: {
                    size: 0,
                    colors: ['#1a4d2e'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#revenueChart"), options);
            chart.render();
        });
    </script>
</div>
