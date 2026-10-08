/* ==========================================================================
   KSP CORE ENTERPRISE SYSTEM - CHART ENGINE (Chart.js 4.x)
   Visualisasi Arus Likuiditas, Portofolio Simpanan, & Kinerja Rasio
   ========================================================================== */

const KspCharts = {
  liquidityChartInstance: null,
  savingsDonutInstance: null,

  initDashboardCharts: function() {
    this.renderLiquidityCashflow();
    this.renderSavingsPortfolioDonut();
  },

  // 1. Visualisasi Arus Likuiditas (Inflow vs Outflow 6 Bulan)
  renderLiquidityCashflow: function() {
    const canvas = document.getElementById('chartLiquidityFlow');
    if (!canvas) return;

    if (this.liquidityChartInstance) {
      this.liquidityChartInstance.destroy();
    }

    const ctx = canvas.getContext('2d');
    
    // Inflow: Setoran simpanan + Pembayaran angsuran
    // Outflow: Penarikan simpanan + Pencairan pinjaman baru
    const months = ['Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'];
    const inflowData = [4200, 4650, 4800, 5200, 5800, 6450]; // In Millions IDR
    const outflowData = [3800, 4100, 4350, 4600, 4900, 5100];
    const netGrowth = inflowData.map((inf, idx) => inf - outflowData[idx]);

    this.liquidityChartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: months,
        datasets: [
          {
            type: 'line',
            label: 'Net Likuiditas (Surplus/Defisit)',
            data: netGrowth,
            borderColor: '#F59E0B',
            backgroundColor: '#F59E0B',
            borderWidth: 2.5,
            pointBackgroundColor: '#F59E0B',
            pointRadius: 4,
            tension: 0.35,
            yAxisID: 'y'
          },
          {
            type: 'bar',
            label: 'Kas Masuk (Inflow)',
            data: inflowData,
            backgroundColor: 'rgba(16, 185, 129, 0.85)',
            borderColor: '#10B981',
            borderRadius: 6,
            borderWidth: 1,
            yAxisID: 'y'
          },
          {
            type: 'bar',
            label: 'Kas Keluar (Outflow)',
            data: outflowData,
            backgroundColor: 'rgba(239, 68, 68, 0.75)',
            borderColor: '#EF4444',
            borderRadius: 6,
            borderWidth: 1,
            yAxisID: 'y'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',
          intersect: false
        },
        plugins: {
          legend: {
            position: 'top',
            labels: {
              color: '#94A3B8',
              font: { family: 'Plus Jakarta Sans', size: 12, weight: '600' },
              usePointStyle: true,
              pointStyle: 'circle'
            }
          },
          tooltip: {
            backgroundColor: '#162244',
            titleColor: '#F8FAFC',
            bodyColor: '#94A3B8',
            borderColor: 'rgba(255, 255, 255, 0.1)',
            borderWidth: 1,
            padding: 12,
            callbacks: {
              label: function(context) {
                const fullNominal = (context.parsed.y * 1000000).toLocaleString('id-ID');
                return `${context.dataset.label}: Rp ${fullNominal}`;
              }
            }
          }
        },
        scales: {
          x: {
            grid: { color: 'rgba(255, 255, 255, 0.05)' },
            ticks: { color: '#64748B', font: { family: 'Plus Jakarta Sans', weight: '500' } }
          },
          y: {
            grid: { color: 'rgba(255, 255, 255, 0.05)' },
            ticks: {
              color: '#64748B',
              font: { family: 'JetBrains Mono', size: 11 },
              callback: function(value) { return 'Rp ' + (value * 1000000).toLocaleString('id-ID'); }
            }
          }
        }
      }
    });
  },

  // 2. Distribusi Portofolio Simpanan Terhimpun (Donut Chart)
  renderSavingsPortfolioDonut: function() {
    const canvas = document.getElementById('chartSavingsPortfolio');
    if (!canvas) return;

    if (this.savingsDonutInstance) {
      this.savingsDonutInstance.destroy();
    }

    const ctx = canvas.getContext('2d');

    this.savingsDonutInstance = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Simpanan Wajib', 'Simpanan Sukarela (SiSuka)', 'Deposito Berjangka (Sijaka)', 'Simpanan Pokok'],
        datasets: [{
          data: [6850, 5100, 4800, 1500], // In Millions IDR
          backgroundColor: [
            '#10B981', // Emerald
            '#3B82F6', // Blue
            '#F59E0B', // Amber
            '#64748B'  // Slate
          ],
          borderColor: '#101935',
          borderWidth: 3,
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#94A3B8',
              font: { family: 'Plus Jakarta Sans', size: 11, weight: '500' },
              padding: 14,
              usePointStyle: true,
              pointStyle: 'circle'
            }
          },
          tooltip: {
            backgroundColor: '#162244',
            titleColor: '#F8FAFC',
            bodyColor: '#94A3B8',
            borderColor: 'rgba(255, 255, 255, 0.1)',
            borderWidth: 1,
            padding: 12,
            callbacks: {
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percent = ((context.parsed / total) * 100).toFixed(1);
                const fullNominal = (context.parsed * 1000000).toLocaleString('id-ID');
                return `${context.label}: Rp ${fullNominal} (${percent}%)`;
              }
            }
          }
        },
        cutout: '68%'
      }
    });
  }
};
