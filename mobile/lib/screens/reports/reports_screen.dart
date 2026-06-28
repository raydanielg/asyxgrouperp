import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:fl_chart/fl_chart.dart';
import '../../core/theme.dart';
import '../../core/services/api_service.dart';
import '../../core/utils/helpers.dart';
import '../../core/widgets/kpi_card.dart';

class ReportsScreen extends StatefulWidget {
  const ReportsScreen({super.key});

  @override
  State<ReportsScreen> createState() => _ReportsScreenState();
}

class _ReportsScreenState extends State<ReportsScreen> {
  Map<String, dynamic>? _financial;
  Map<String, dynamic>? _chartData;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _loadReports();
  }

  Future<void> _loadReports() async {
    setState(() => _loading = true);
    try {
      final finRes = await ApiService().get('/financial-summary');
      final dashRes = await ApiService().get('/dashboard/role');
      setState(() {
        _financial = finRes.data;
        _chartData = dashRes.data?['chartData'];
        _loading = false;
      });
    } catch (e) {
      setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Reports & Analytics')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadReports,
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Financial Summary', style: GoogleFonts.inter(fontSize: 18, fontWeight: FontWeight.w800)),
                    const SizedBox(height: 16),
                    GridView.count(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      crossAxisCount: 2,
                      mainAxisSpacing: 12,
                      crossAxisSpacing: 12,
                      childAspectRatio: 1.35,
                      children: [
                        KpiCard(label: 'Revenue', value: _financial?['total_revenue'] ?? 0, icon: Icons.trending_up, color: AppTheme.successColor, isMoney: true),
                        KpiCard(label: 'Expenses', value: _financial?['total_expenses'] ?? 0, icon: Icons.payments, color: AppTheme.dangerColor, isMoney: true),
                        KpiCard(label: 'Net Profit', value: _financial?['net_profit'] ?? 0, icon: Icons.account_balance, color: AppTheme.accentColor, isMoney: true),
                        KpiCard(label: 'Bank Balance', value: _financial?['bank_balance'] ?? 0, icon: Icons.account_balance_wallet, color: const Color(0xFF8B5CF6), isMoney: true),
                      ],
                    ),
                    const SizedBox(height: 24),
                    Text('14-Day Trend', style: GoogleFonts.inter(fontSize: 16, fontWeight: FontWeight.w700)),
                    const SizedBox(height: 16),
                    Container(
                      height: 220,
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: AppTheme.borderColor),
                      ),
                      child: _buildChart(),
                    ),
                    const SizedBox(height: 24),
                    Text('Quick Reports', style: GoogleFonts.inter(fontSize: 16, fontWeight: FontWeight.w700)),
                    const SizedBox(height: 12),
                    _reportTile('Sales Report', 'View all sales transactions', Icons.receipt_long, AppTheme.successColor),
                    const SizedBox(height: 8),
                    _reportTile('Expense Report', 'Track all expenses by category', Icons.payments, AppTheme.dangerColor),
                    const SizedBox(height: 8),
                    _reportTile('Project Report', 'Project status and profitability', Icons.folder, AppTheme.accentColor),
                    const SizedBox(height: 8),
                    _reportTile('Employee Report', 'Attendance and performance', Icons.people, AppTheme.warningColor),
                    const SizedBox(height: 8),
                    _reportTile('Inventory Report', 'Stock levels and movements', Icons.inventory_2, const Color(0xFF8B5CF6)),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildChart() {
    final values = (_chartData?['values'] as List?)?.map((e) => (e as num).toDouble()).toList() ?? List.filled(14, 0.0);
    final labels = (_chartData?['labels'] as List?)?.map((e) => e.toString()).toList() ?? [];

    if (values.isEmpty) {
      return const Center(child: Text('No chart data available'));
    }

    final maxY = values.reduce((a, b) => a > b ? a : b);

    return BarChart(
      BarChartData(
        maxY: maxY > 0 ? maxY * 1.2 : 100,
        barTouchData: BarTouchData(enabled: true),
        titlesData: FlTitlesData(
          show: true,
          bottomTitles: AxisTitles(
            sideTitles: SideTitles(
              showTitles: true,
              getTitlesWidget: (value, meta) {
                final idx = value.toInt();
                if (idx >= 0 && idx < labels.length && idx % 3 == 0) {
                  return Padding(
                    padding: const EdgeInsets.only(top: 8),
                    child: Text(labels[idx].substring(0, 2), style: GoogleFonts.inter(fontSize: 9, color: AppTheme.textMuted)),
                  );
                }
                return const SizedBox.shrink();
              },
            ),
          ),
          leftTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
          topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
          rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
        ),
        borderData: FlBorderData(show: false),
        gridData: const FlGridData(show: false),
        barGroups: List.generate(values.length, (i) {
          return BarChartGroupData(
            x: i,
            barRods: [
              BarChartRodData(
                toY: values[i],
                color: AppTheme.primaryColor,
                width: 12,
                borderRadius: BorderRadius.circular(4),
              ),
            ],
          );
        }),
      ),
    );
  }

  Widget _reportTile(String title, String subtitle, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppTheme.borderColor),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(10)),
            child: Icon(icon, color: color, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: GoogleFonts.inter(fontSize: 13, fontWeight: FontWeight.w600)),
                Text(subtitle, style: GoogleFonts.inter(fontSize: 11, color: AppTheme.textSecondary)),
              ],
            ),
          ),
          const Icon(Icons.chevron_right, color: AppTheme.textMuted, size: 20),
        ],
      ),
    );
  }
}
