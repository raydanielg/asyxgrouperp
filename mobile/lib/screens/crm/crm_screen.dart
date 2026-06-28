import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../core/theme.dart';
import '../../core/services/api_service.dart';
import '../../core/utils/helpers.dart';
import '../../core/widgets/app_widgets.dart';

class CrmScreen extends StatefulWidget {
  const CrmScreen({super.key});

  @override
  State<CrmScreen> createState() => _CrmScreenState();
}

class _CrmScreenState extends State<CrmScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  List<dynamic> _leads = [];
  List<dynamic> _deals = [];
  List<dynamic> _contacts = [];
  List<dynamic> _contracts = [];
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
      final leadsRes = await ApiService().get('/crm/leads');
      final dealsRes = await ApiService().get('/crm/deals');
      final contactsRes = await ApiService().get('/crm/contacts');
      final contractsRes = await ApiService().get('/crm/contracts');
      setState(() {
        _leads = leadsRes.data['data'] ?? leadsRes.data ?? [];
        _deals = dealsRes.data['data'] ?? dealsRes.data ?? [];
        _contacts = contactsRes.data['data'] ?? contactsRes.data ?? [];
        _contracts = contractsRes.data['data'] ?? contractsRes.data ?? [];
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
        title: const Text('CRM'),
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppTheme.primaryColor,
          unselectedLabelColor: AppTheme.textMuted,
          indicatorColor: AppTheme.primaryColor,
          labelStyle: GoogleFonts.inter(fontSize: 12, fontWeight: FontWeight.w600),
          tabs: const [
            Tab(text: 'Leads'),
            Tab(text: 'Deals'),
            Tab(text: 'Contacts'),
            Tab(text: 'Contracts'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildList(_leads, Icons.trending_up, AppTheme.warningColor, 'leads'),
          _buildList(_deals, Icons.handshake, AppTheme.accentColor, 'deals'),
          _buildList(_contacts, Icons.people, AppTheme.primaryColor, 'contacts'),
          _buildList(_contracts, Icons.description, const Color(0xFF8B5CF6), 'contracts'),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {},
        icon: const Icon(Icons.add),
        label: const Text('Add'),
      ),
    );
  }

  Widget _buildList(List<dynamic> items, IconData icon, Color color, String type) {
    if (_loading) return const Center(child: CircularProgressIndicator());
    if (items.isEmpty) return EmptyState(icon: icon, title: 'No ${type[0].toUpperCase()}${type.substring(1)}', subtitle: 'Add your first $type');

    return RefreshIndicator(
      onRefresh: _loadData,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: items.length,
        separatorBuilder: (_, __) => const SizedBox(height: 8),
        itemBuilder: (context, index) {
          final item = items[index];
          String subtitle = '';
          if (type == 'leads') subtitle = '${item['company'] ?? ''} • ${item['source'] ?? ''}';
          if (type == 'deals') subtitle = AppHelpers.formatMoney(item['value'] ?? 0);
          if (type == 'contacts') subtitle = '${item['email'] ?? ''} • ${item['phone'] ?? ''}';
          if (type == 'contracts') subtitle = '${item['client'] ?? ''} • ${AppHelpers.formatDate(item['start_date'])}';

          return DataListTile(
            title: item['name'] ?? item['title'] ?? '#${item['id']}',
            subtitle: subtitle,
            status: item['status'],
            leadingIcon: icon,
            leadingColor: color,
            onTap: () {},
          );
        },
      ),
    );
  }
}
