import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../core/theme.dart';
import '../../core/services/api_service.dart';
import '../../core/utils/helpers.dart';
import '../../core/widgets/app_widgets.dart';

class ProjectsScreen extends StatefulWidget {
  const ProjectsScreen({super.key});

  @override
  State<ProjectsScreen> createState() => _ProjectsScreenState();
}

class _ProjectsScreenState extends State<ProjectsScreen> {
  List<dynamic> _projects = [];
  bool _loading = true;
  String _filter = 'all';

  @override
  void initState() {
    super.initState();
    _loadProjects();
  }

  Future<void> _loadProjects() async {
    setState(() => _loading = true);
    try {
      final res = await ApiService().get('/projects');
      setState(() {
        _projects = res.data['data'] ?? res.data ?? [];
        _loading = false;
      });
    } catch (e) {
      setState(() => _loading = false);
    }
  }

  List<dynamic> get _filteredProjects {
    if (_filter == 'all') return _projects;
    return _projects.where((p) => p['status'] == _filter).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Projects'),
        actions: [
          PopupMenuButton<String>(
            icon: const Icon(Icons.filter_list),
            onSelected: (v) => setState(() => _filter = v),
            itemBuilder: (_) => [
              const PopupMenuItem(value: 'all', child: Text('All')),
              const PopupMenuItem(value: 'in_progress', child: Text('In Progress')),
              const PopupMenuItem(value: 'completed', child: Text('Completed')),
              const PopupMenuItem(value: 'pending', child: Text('Pending')),
            ],
          ),
        ],
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _filteredProjects.isEmpty
              ? const EmptyState(icon: Icons.folder, title: 'No Projects', subtitle: 'Create your first project')
              : RefreshIndicator(
                  onRefresh: _loadProjects,
                  child: ListView.separated(
                    padding: const EdgeInsets.all(16),
                    itemCount: _filteredProjects.length,
                    separatorBuilder: (_, ___) => const SizedBox(height: 10),
                    itemBuilder: (context, index) {
                      final project = _filteredProjects[index];
                      return _buildProjectCard(project);
                    },
                  ),
                ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {},
        icon: const Icon(Icons.add),
        label: const Text('New Project'),
      ),
    );
  }

  Widget _buildProjectCard(Map<String, dynamic> project) {
    final progress = (project['progress'] ?? 0).toDouble();
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppTheme.borderColor),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 8, offset: const Offset(0, 2))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: Text(
                  project['name'] ?? 'Untitled Project',
                  style: GoogleFonts.inter(fontSize: 14, fontWeight: FontWeight.w700, color: AppTheme.textPrimary),
                ),
              ),
              StatusBadge(status: project['status'] ?? 'pending'),
            ],
          ),
          if (project['client'] != null || project['description'] != null) ...[
            const SizedBox(height: 6),
            Text(
              project['client'] ?? project['description'] ?? '',
              style: GoogleFonts.inter(fontSize: 12, color: AppTheme.textSecondary),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ],
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Progress', style: GoogleFonts.inter(fontSize: 10, color: AppTheme.textMuted)),
                    const SizedBox(height: 4),
                    LinearProgressIndicator(
                      value: progress / 100,
                      backgroundColor: AppTheme.borderColor,
                      valueColor: AlwaysStoppedAnimation(progress >= 100 ? AppTheme.successColor : AppTheme.primaryColor),
                      borderRadius: BorderRadius.circular(4),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 12),
              Text('${progress.toInt()}%', style: GoogleFonts.inter(fontSize: 13, fontWeight: FontWeight.w700, color: AppTheme.primaryColor)),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Icon(Icons.calendar_today, size: 12, color: AppTheme.textMuted),
              const SizedBox(width: 4),
              Text(AppHelpers.formatDate(project['start_date']), style: GoogleFonts.inter(fontSize: 11, color: AppTheme.textSecondary)),
              const SizedBox(width: 12),
              if (project['budget'] != null) ...[
                Icon(Icons.attach_money, size: 12, color: AppTheme.textMuted),
                const SizedBox(width: 2),
                Text(AppHelpers.formatMoney(project['budget']), style: GoogleFonts.inter(fontSize: 11, color: AppTheme.textSecondary)),
              ],
            ],
          ),
        ],
      ),
    );
  }
}
