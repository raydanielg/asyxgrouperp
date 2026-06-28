import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../core/theme.dart';
import '../../core/services/api_service.dart';
import '../../core/utils/helpers.dart';
import '../../core/widgets/kpi_card.dart';
import '../../core/widgets/app_widgets.dart';

class FinanceScreen extends StatefulWidget {
  const FinanceScreen({super.key});

  @override
  State<FinanceScreen> createState() => _FinanceScreenState();
}

class _FinanceScreenState extends State<FinanceScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  Map<String, dynamic>? _summary;
  List<dynamic> _expenses = [];
  List<dynamic> _revenues = [];
  List<dynamic> _salesInvoices = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _loading = true);
    try {
      final summaryRes = await ApiService().get('/financial-summary');
      final expRes = await ApiService().get('/expenses');
      final revRes = await ApiService().get('/revenues');
      final salesRes = await ApiService().get('/sales-invoices');
      setState(() {
        _summary = summaryRes.data;
        _expenses = expRes.data['data'] ?? expRes.data ?? [];
        _revenues = revRes.data['data'] ?? revRes.data ?? [];
        _salesInvoices = salesRes.data['data'] ?? salesRes.data ?? [];
        _loading = false;
      });
    } catch (e) {
      setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Finance'),
        bottom: TabBar(
          controller: _tabController,
          isScrollable: true,
          labelColor: AppTheme.primaryColor,
          unselectedLabelColor: AppTheme.textMuted,
          indicatorColor: AppTheme.primaryColor,
          labelStyle: GoogleFonts.inter(fontSize: 12, fontWeight: FontWeight.w600),
          tabs: const [
            Tab(text: 'Overview'),
            Tab(text: 'Expenses'),
            Tab(text: 'Revenues'),
            Tab(text: 'Invoices'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildOverview(),
          _buildExpensesList(),
          _buildRevenuesList(),
          _buildInvoicesList(),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {},
        icon: const Icon(Icons.add),
        label: const Text('Add'),
      ),
    );
  }

  Widget _buildOverview() {
    if (_loading) return const Center(child: CircularProgressIndicator());

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          GridView.count(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisCount: 2,
            mainAxisSpacing: 12,
            crossAxisSpacing: 12,
            childAspectRatio: 1.35,
            children: [
              KpiCard(label: 'Total Revenue', value: _summary?['total_revenue'] ?? 0, icon: Icons.trending_up, color: AppTheme.successColor, isMoney: true),
              KpiCard(label: 'Total Expenses', value: _summary?['total_expenses'] ?? 0, icon: Icons.payments, color: AppTheme.dangerColor, isMoney: true),
              KpiCard(label: 'Net Profit', value: _summary?['net_profit'] ?? 0, icon: Icons.account_balance, color: AppTheme.accentColor, isMoney: true),
              KpiCard(label: 'Bank Balance', value: _summary?['bank_balance'] ?? 0, icon: Icons.account_balance_wallet, color: const Color(0xFF8B5CF6), isMoney: true),
              KpiCard(label: 'Receivables', value: _summary?['outstanding_receivables'] ?? 0, icon: Icons.call_received, color: AppTheme.warningColor, isMoney: true),
              KpiCard(label: 'Payables', value: _summary?['outstanding_payables'] ?? 0, icon: Icons.call_made, color: AppTheme.dangerColor, isMoney: true),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildExpensesList() {
    if (_loading) return const Center(child: CircularProgressIndicator());
    if (_expenses.isEmpty) return const EmptyState(icon: Icons.payments, title: 'No Expenses', subtitle: 'Record your first expense');

    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: _expenses.length,
      separatorBuilder: (_, __) => const SizedBox(height: 8),
      itemBuilder: (context, index) {
        final exp = _expenses[index];
        return DataListTile(
          title: exp['category'] ?? 'Expense',
          subtitle: '${exp['description'] ?? ''} • ${AppHelpers.formatDate(exp['expense_date'])}',
          trailing: AppHelpers.formatMoney(exp['amount'] ?? 0),
          leadingIcon: Icons.payments,
          leadingColor: AppTheme.dangerColor,
        );
      },
    );
  }

  Widget _buildRevenuesList() {
    if (_loading) return const Center(child: CircularProgressIndicator());
    if (_revenues.isEmpty) return const EmptyState(icon: Icons.trending_up, title: 'No Revenues', subtitle: 'Record your first revenue');

    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: _revenues.length,
      separatorBuilder: (_, __) => const SizedBox(height: 8),
      itemBuilder: (context, index) {
        final rev = _revenues[index];
        return DataListTile(
          title: rev['category'] ?? 'Revenue',
          subtitle: '${rev['description'] ?? ''} • ${AppHelpers.formatDate(rev['revenue_date'])}',
          trailing: AppHelpers.formatMoney(rev['amount'] ?? 0),
          leadingIcon: Icons.trending_up,
          leadingColor: AppTheme.successColor,
        );
      },
    );
  }

  Widget _buildInvoicesList() {
    if (_loading) return const Center(child: CircularProgressIndicator());
    if (_salesInvoices.isEmpty) return const EmptyState(icon: Icons.receipt_long, title: 'No Invoices', subtitle: 'Create your first invoice');

    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: _salesInvoices.length,
      separatorBuilder: (_, __) => const SizedBox(height: 8),
      itemBuilder: (context, index) {
        final inv = _salesInvoices[index];
        return DataListTile(
          title: inv['invoice_number'] ?? '#${inv['id']}',
          subtitle: '${inv['customer']?['name'] ?? inv['customer_name'] ?? 'Customer'} • ${AppHelpers.formatDate(inv['created_at'])}',
          trailing: AppHelpers.formatMoney(inv['total_amount'] ?? 0),
          status: inv['status'],
          leadingIcon: Icons.receipt_long,
          leadingColor: AppTheme.accentColor,
        );
      },
    );
  }
}
