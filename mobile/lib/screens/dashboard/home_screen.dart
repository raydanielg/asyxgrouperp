import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../../core/theme.dart';
import '../../core/providers/auth_provider.dart';
import '../../core/services/api_service.dart';
import '../../core/utils/helpers.dart';
import '../../core/widgets/kpi_card.dart';
import '../../core/widgets/app_widgets.dart';
import '../employees/employees_screen.dart';
import '../finance/finance_screen.dart';
import '../projects/projects_screen.dart';
import '../crm/crm_screen.dart';
import '../inventory/inventory_screen.dart';
import '../pos/pos_screen.dart';
import '../helpdesk/helpdesk_screen.dart';
import '../reports/reports_screen.dart';
import '../settings/settings_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _currentIndex = 0;
  Map<String, dynamic>? _dashboardData;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _loadDashboard();
  }

  Future<void> _loadDashboard() async {
    setState(() => _loading = true);
    try {
      final response = await ApiService().get('/dashboard/role');
      setState(() {
        _dashboardData = response.data;
        _loading = false;
      });
    } catch (e) {
      setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(
        index: _currentIndex,
        children: [
          _buildDashboard(),
          const ModulesScreen(),
          const ReportsScreen(),
          const SettingsScreen(),
        ],
      ),
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, -2))],
        ),
        child: BottomNavigationBar(
          currentIndex: _currentIndex,
          onTap: (i) => setState(() => _currentIndex = i),
          items: const [
            BottomNavigationBarItem(icon: Icon(Icons.dashboard_rounded), label: 'Dashboard'),
            BottomNavigationBarItem(icon: Icon(Icons.apps_rounded), label: 'Modules'),
            BottomNavigationBarItem(icon: Icon(Icons.bar_chart_rounded), label: 'Reports'),
            BottomNavigationBarItem(icon: Icon(Icons.settings_rounded), label: 'Settings'),
          ],
        ),
      ),
    );
  }

  Widget _buildDashboard() {
    final user = context.watch<AuthProvider>().user;

    return RefreshIndicator(
      onRefresh: _loadDashboard,
      color: AppTheme.primaryColor,
      child: CustomScrollView(
        slivers: [
          // App Bar with gradient
          SliverAppBar(
            expandedHeight: 140,
            floating: false,
            pinned: true,
            automaticallyImplyLeading: false,
            backgroundColor: AppTheme.primaryDark,
            flexibleSpace: FlexibleSpaceBar(
              background: Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [AppTheme.primaryDark, AppTheme.primaryColor],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
                child: SafeArea(
                  child: Padding(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        Row(
                          children: [
                            CircleAvatar(
                              radius: 22,
                              backgroundColor: Colors.white.withOpacity(0.2),
                              child: Text(
                                AppHelpers.initials(user?.name ?? 'U'),
                                style: GoogleFonts.inter(fontSize: 14, fontWeight: FontWeight.w700, color: Colors.white),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    'Welcome, ${user?.name ?? 'User'}',
                                    style: GoogleFonts.inter(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.white),
                                  ),
                                  Text(
                                    user?.roleLabel ?? 'Employee',
                                    style: GoogleFonts.inter(fontSize: 12, color: Colors.white70),
                                  ),
                                ],
                              ),
                            ),
                            IconButton(
                              icon: const Icon(Icons.notifications_outlined, color: Colors.white),
                              onPressed: () {},
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),

          // KPI Cards
          if (_loading)
            SliverPadding(
              padding: const EdgeInsets.all(16),
              sliver: SliverGrid(
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  mainAxisSpacing: 12,
                  crossAxisSpacing: 12,
                  childAspectRatio: 1.4,
                ),
                delegate: SliverChildBuilderDelegate(
                  (_, __) => const KpiCardShimmer(),
                  childCount: 4,
                ),
              ),
            )
          else ...[
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 8),
                child: Text('Overview', style: GoogleFonts.inter(fontSize: 18, fontWeight: FontWeight.w800, color: AppTheme.textPrimary)),
              ),
            ),
            SliverPadding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              sliver: SliverGrid(
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  mainAxisSpacing: 12,
                  crossAxisSpacing: 12,
                  childAspectRatio: 1.35,
                ),
                delegate: SliverChildBuilderDelegate(
                  (context, index) {
                    final cards = (_dashboardData?['kpiCards'] as List?) ?? [];
                    if (index >= cards.length) return null;
                    final card = cards[index];
                    return KpiCard(
                      label: card['label'] ?? '',
                      value: card['value'] ?? 0,
                      icon: _getIconData(card['icon'] ?? 'info'),
                      color: _parseColor(card['color'] ?? '#10B981'),
                      isMoney: card['isMoney'] == true,
                    );
                  },
                  childCount: ((_dashboardData?['kpiCards'] as List?) ?? []).length,
                ),
              ),
            ),

            // Quick Actions
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(16, 24, 16, 12),
                child: Text('Quick Actions', style: GoogleFonts.inter(fontSize: 16, fontWeight: FontWeight.w700, color: AppTheme.textPrimary)),
              ),
            ),
            SliverToBoxAdapter(
              child: SizedBox(
                height: 100,
                child: ListView.separated(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  itemCount: ((_dashboardData?['quickActions'] as List?) ?? []).length,
                  separatorBuilder: (_, __) => const SizedBox(width: 16),
                  itemBuilder: (context, index) {
                    final actions = (_dashboardData?['quickActions'] as List?) ?? [];
                    final action = actions[index];
                    return QuickActionButton(
                      label: action['label'] ?? '',
                      icon: _getIconData(action['icon'] ?? 'apps'),
                      color: AppTheme.primaryColor,
                      onTap: () => _navigateToModule(action['route'] ?? ''),
                    );
                  },
                ),
              ),
            ),

            // Recent Activity
            const SliverToBoxAdapter(child: SectionHeader(title: 'Recent Activity')),
            SliverPadding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              sliver: SliverList(
                delegate: SliverChildBuilderDelegate(
                  (context, index) {
                    final activity = _dashboardData?['recentActivity'] as Map<String, dynamic>? ?? {};
                    final allItems = <Map<String, dynamic>>[];

                    activity.forEach((key, value) {
                      if (value is List) {
                        for (var item in value.take(3)) {
                          allItems.add({'type': key, ...Map<String, dynamic>.from(item)});
                        }
                      }
                    });

                    if (index >= allItems.length) return null;
                    final item = allItems[index];
                    return Padding(
                      padding: const EdgeInsets.only(bottom: 8),
                      child: DataListTile(
                        title: item['title'] ?? item['name'] ?? item['subject'] ?? item['invoice_number'] ?? '#${item['id']}',
                        subtitle: AppHelpers.timeAgo(item['created_at']),
                        status: item['status'],
                        leadingIcon: _getActivityIcon(item['type']),
                        leadingColor: _getActivityColor(item['type']),
                      ),
                    );
                  },
                  childCount: _getActivityCount(),
                ),
              ),
            ),
            const SliverToBoxAdapter(child: SizedBox(height: 20)),
          ],
        ],
      ),
    );
  }

  int _getActivityCount() {
    final activity = _dashboardData?['recentActivity'] as Map<String, dynamic>? ?? {};
    int count = 0;
    activity.forEach((key, value) {
      if (value is List) count += (value.length > 3 ? 3 : value.length);
    });
    return count;
  }

  IconData _getActivityIcon(String? type) {
    switch (type) {
      case 'recentSales': return Icons.receipt_long;
      case 'recentTickets': return Icons.confirmation_number;
      case 'recentProjects': return Icons.folder;
      case 'recentExpenses': return Icons.payments;
      case 'recentRevenues': return Icons.trending_up;
      case 'recentEmployees': return Icons.people;
      case 'recentLeaves': return Icons.event_busy;
      default: return Icons.history;
    }
  }

  Color _getActivityColor(String? type) {
    switch (type) {
      case 'recentSales': return AppTheme.successColor;
      case 'recentTickets': return AppTheme.warningColor;
      case 'recentProjects': return AppTheme.accentColor;
      case 'recentExpenses': return AppTheme.dangerColor;
      case 'recentRevenues': return AppTheme.primaryColor;
      default: return AppTheme.textSecondary;
    }
  }

  void _navigateToModule(String route) {
    switch (route) {
      case '/employees':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const EmployeesScreen()));
        break;
      case '/sales-invoices':
      case '/expenses':
      case '/revenues':
      case '/purchase-invoices':
      case '/bank-accounts':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const FinanceScreen()));
        break;
      case '/projects':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const ProjectsScreen()));
        break;
      case '/pos':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const PosScreen()));
        break;
      case '/products':
        Navigator.push(context, MaterialPageRoute(builder: (_) => const InventoryScreen()));
        break;
      default:
        break;
    }
  }

  IconData _getIconData(String name) {
    final map = {
      'people': Icons.people,
      'receipt_long': Icons.receipt_long,
      'payments': Icons.payments,
      'trending_up': Icons.trending_up,
      'confirmation_number': Icons.confirmation_number,
      'folder_open': Icons.folder_open,
      'folder': Icons.folder,
      'account_balance': Icons.account_balance,
      'pending_actions': Icons.pending_actions,
      'warning': Icons.warning,
      'check_circle': Icons.check_circle,
      'cancel': Icons.cancel,
      'event_busy': Icons.event_busy,
      'event_available': Icons.event_available,
      'point_of_sale': Icons.point_of_sale,
      'receipt': Icons.receipt,
      'inventory_2': Icons.inventory_2,
      'inventory': Icons.inventory,
      'badge': Icons.badge,
      'bar_chart': Icons.bar_chart,
      'settings': Icons.settings,
      'dashboard': Icons.dashboard,
      'person': Icons.person,
      'person_add': Icons.person_add,
      'assessment': Icons.assessment,
      'description': Icons.description,
      'shopping_bag': Icons.shopping_bag,
      'apps': Icons.apps,
      'verified': Icons.verified,
      'info': Icons.info,
    };
    return map[name] ?? Icons.circle;
  }

  Color _parseColor(String hex) {
    try {
      return Color(int.parse(hex.replaceFirst('#', '0xFF')));
    } catch (_) {
      return AppTheme.primaryColor;
    }
  }
}

class ModulesScreen extends StatelessWidget {
  const ModulesScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final modules = [
      {'title': 'Employees', 'icon': Icons.people, 'color': const Color(0xFF10B981), 'screen': const EmployeesScreen()},
      {'title': 'Finance', 'icon': Icons.account_balance, 'color': const Color(0xFF0EA5E9), 'screen': const FinanceScreen()},
      {'title': 'Projects', 'icon': Icons.folder, 'color': const Color(0xFF8B5CF6), 'screen': const ProjectsScreen()},
      {'title': 'CRM', 'icon': Icons.handshake, 'color': const Color(0xFFF59E0B), 'screen': const CrmScreen()},
      {'title': 'Inventory', 'icon': Icons.inventory_2, 'color': const Color(0xFF06B6D4), 'screen': const InventoryScreen()},
      {'title': 'POS', 'icon': Icons.point_of_sale, 'color': const Color(0xFFEC4899), 'screen': const PosScreen()},
      {'title': 'Helpdesk', 'icon': Icons.support_agent, 'color': const Color(0xFFEF4444), 'screen': const HelpdeskScreen()},
      {'title': 'Reports', 'icon': Icons.bar_chart, 'color': const Color(0xFF14B8A6), 'screen': const ReportsScreen()},
    ];

    return Scaffold(
      appBar: AppBar(title: const Text('Modules')),
      body: GridView.builder(
        padding: const EdgeInsets.all(16),
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          mainAxisSpacing: 16,
          crossAxisSpacing: 16,
          childAspectRatio: 1.2,
        ),
        itemCount: modules.length,
        itemBuilder: (context, index) {
          final m = modules[index];
          return GestureDetector(
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => m['screen'] as Widget)),
            child: Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: (m['color'] as Color).withOpacity(0.2)),
                boxShadow: [BoxShadow(color: (m['color'] as Color).withOpacity(0.08), blurRadius: 12, offset: const Offset(0, 4))],
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: (m['color'] as Color).withOpacity(0.1),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Icon(m['icon'] as IconData, color: m['color'] as Color, size: 28),
                  ),
                  const SizedBox(height: 12),
                  Text(
                    m['title'] as String,
                    style: GoogleFonts.inter(fontSize: 14, fontWeight: FontWeight.w700, color: AppTheme.textPrimary),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
