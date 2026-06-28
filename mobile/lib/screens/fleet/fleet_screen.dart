import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../core/theme.dart';
import '../../core/services/api_service.dart';
import '../../core/utils/helpers.dart';
import '../../core/widgets/app_widgets.dart';

class FleetScreen extends StatefulWidget {
  const FleetScreen({super.key});

  @override
  State<FleetScreen> createState() => _FleetScreenState();
}

class _FleetScreenState extends State<FleetScreen> {
  List<dynamic> _vehicles = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _loadVehicles();
  }

  Future<void> _loadVehicles() async {
    setState(() => _loading = true);
    try {
      final res = await ApiService().get('/fleet');
      setState(() {
        _vehicles = res.data['data'] ?? res.data ?? [];
        _loading = false;
      });
    } catch (e) {
      setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Fleet Management')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _vehicles.isEmpty
              ? const EmptyState(icon: Icons.directions_car, title: 'No Vehicles', subtitle: 'Add your first vehicle to the fleet')
              : RefreshIndicator(
                  onRefresh: _loadVehicles,
                  child: ListView.separated(
                    padding: const EdgeInsets.all(16),
                    itemCount: _vehicles.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 10),
                    itemBuilder: (context, index) {
                      final v = _vehicles[index];
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
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(color: AppTheme.accentColor.withOpacity(0.1), borderRadius: BorderRadius.circular(12)),
                              child: const Icon(Icons.directions_car, color: AppTheme.accentColor, size: 24),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text('${v['make'] ?? ''} ${v['model'] ?? ''}', style: GoogleFonts.inter(fontSize: 14, fontWeight: FontWeight.w700)),
                                  const SizedBox(height: 2),
                                  Text(v['registration_number'] ?? '', style: GoogleFonts.inter(fontSize: 12, color: AppTheme.textSecondary)),
                                  if (v['year'] != null)
                                    Text('Year: ${v['year']}', style: GoogleFonts.inter(fontSize: 11, color: AppTheme.textMuted)),
                                ],
                              ),
                            ),
                            StatusBadge(status: v['status'] ?? 'active'),
                          ],
                        ),
                      );
                    },
                  ),
                ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {},
        icon: const Icon(Icons.add),
        label: const Text('Add Vehicle'),
      ),
    );
  }
}
