import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../core/theme.dart';
import '../../core/services/api_service.dart';
import '../../core/utils/helpers.dart';
import '../../core/widgets/app_widgets.dart';

class EmployeesScreen extends StatefulWidget {
  const EmployeesScreen({super.key});

  @override
  State<EmployeesScreen> createState() => _EmployeesScreenState();
}

class _EmployeesScreenState extends State<EmployeesScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  List<dynamic> _employees = [];
  List<dynamic> _attendance = [];
  List<dynamic> _leaves = [];
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
      final empRes = await ApiService().get('/employees');
      final attRes = await ApiService().get('/attendance/today');
      final leaveRes = await ApiService().get('/leaves', params: {'status': 'pending'});
      setState(() {
        _employees = empRes.data['data'] ?? empRes.data ?? [];
        _attendance = attRes.data['records'] ?? [];
        _leaves = leaveRes.data['data'] ?? leaveRes.data ?? [];
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
        title: const Text('HR Management'),
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppTheme.primaryColor,
          unselectedLabelColor: AppTheme.textMuted,
          indicatorColor: AppTheme.primaryColor,
          labelStyle: GoogleFonts.inter(fontSize: 12, fontWeight: FontWeight.w600),
          tabs: const [
            Tab(text: 'Employees'),
            Tab(text: 'Attendance'),
            Tab(text: 'Leaves'),
            Tab(text: 'Payroll'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildEmployeeList(),
          _buildAttendanceList(),
          _buildLeavesList(),
          _buildPayrollTab(),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {},
        icon: const Icon(Icons.add),
        label: const Text('Add'),
      ),
    );
  }

  Widget _buildEmployeeList() {
    if (_loading) return const Center(child: CircularProgressIndicator());
    if (_employees.isEmpty) return const EmptyState(icon: Icons.people, title: 'No Employees', subtitle: 'Add your first employee to get started');

    return RefreshIndicator(
      onRefresh: _loadData,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: _employees.length,
        separatorBuilder: (_, __) => const SizedBox(height: 8),
        itemBuilder: (context, index) {
          final emp = _employees[index];
          return DataListTile(
            title: emp['name'] ?? 'Unknown',
            subtitle: '${emp['position'] ?? 'N/A'} • ${emp['department'] ?? ''}',
            status: emp['status'] ?? 'active',
            leadingIcon: Icons.person,
            leadingColor: AppTheme.primaryColor,
            onTap: () {},
          );
        },
      ),
    );
  }

  Widget _buildAttendanceList() {
    if (_loading) return const Center(child: CircularProgressIndicator());
    if (_attendance.isEmpty) return const EmptyState(icon: Icons.event_available, title: 'No Records', subtitle: 'No attendance records for today');

    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: _attendance.length,
      separatorBuilder: (_, __) => const SizedBox(height: 8),
      itemBuilder: (context, index) {
        final rec = _attendance[index];
        final emp = rec['employee'];
        return DataListTile(
          title: emp?['name'] ?? 'Employee #${rec['employee_id']}',
          subtitle: 'In: ${rec['clock_in'] ?? '-'} | Out: ${rec['clock_out'] ?? '-'}',
          status: rec['status'] ?? 'present',
          leadingIcon: Icons.access_time,
          leadingColor: AppHelpers.statusColor(rec['status']),
        );
      },
    );
  }

  Widget _buildLeavesList() {
    if (_loading) return const Center(child: CircularProgressIndicator());
    if (_leaves.isEmpty) return const EmptyState(icon: Icons.event_busy, title: 'No Pending Leaves', subtitle: 'All leave requests have been processed');

    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: _leaves.length,
      separatorBuilder: (_, __) => const SizedBox(height: 8),
      itemBuilder: (context, index) {
        final leave = _leaves[index];
        final emp = leave['employee'];
        return DataListTile(
          title: emp?['name'] ?? 'Employee',
          subtitle: '${leave['leave_type'] ?? 'Leave'} • ${AppHelpers.formatDate(leave['start_date'])} - ${AppHelpers.formatDate(leave['end_date'])}',
          status: leave['status'] ?? 'pending',
          leadingIcon: Icons.event_busy,
          leadingColor: AppTheme.warningColor,
          onTap: () {},
        );
      },
    );
  }

  Widget _buildPayrollTab() {
    return const Center(
      child: EmptyState(
        icon: Icons.payments,
        title: 'Payroll',
        subtitle: 'Manage monthly payroll for all employees',
        actionText: 'Generate Payroll',
      ),
    );
  }
}
