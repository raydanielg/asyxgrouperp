import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../core/theme.dart';
import '../../core/services/api_service.dart';
import '../../core/utils/helpers.dart';
import '../../core/widgets/app_widgets.dart';

class InventoryScreen extends StatefulWidget {
  const InventoryScreen({super.key});

  @override
  State<InventoryScreen> createState() => _InventoryScreenState();
}

class _InventoryScreenState extends State<InventoryScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  List<dynamic> _products = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _loading = true);
    try {
      final res = await ApiService().get('/products');
      setState(() {
        _products = res.data['data'] ?? res.data ?? [];
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
        title: const Text('Inventory'),
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppTheme.primaryColor,
          unselectedLabelColor: AppTheme.textMuted,
          indicatorColor: AppTheme.primaryColor,
          labelStyle: GoogleFonts.inter(fontSize: 12, fontWeight: FontWeight.w600),
          tabs: const [
            Tab(text: 'Products'),
            Tab(text: 'Low Stock'),
            Tab(text: 'Movements'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildProductList(_products),
          _buildProductList(_products.where((p) {
            final stock = p['stock_quantity'] ?? 0;
            final reorder = p['reorder_level'] ?? 0;
            return stock <= reorder && reorder > 0;
          }).toList()),
          const EmptyState(icon: Icons.swap_vert, title: 'Stock Movements', subtitle: 'View all stock in/out transactions'),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {},
        icon: const Icon(Icons.add),
        label: const Text('Add Product'),
      ),
    );
  }

  Widget _buildProductList(List<dynamic> products) {
    if (_loading) return const Center(child: CircularProgressIndicator());
    if (products.isEmpty) return const EmptyState(icon: Icons.inventory_2, title: 'No Products', subtitle: 'Add your first product');

    return RefreshIndicator(
      onRefresh: _loadData,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: products.length,
        separatorBuilder: (_, __) => const SizedBox(height: 8),
        itemBuilder: (context, index) {
          final p = products[index];
          final stock = p['stock_quantity'] ?? 0;
          final reorder = p['reorder_level'] ?? 0;
          final isLow = stock <= reorder && reorder > 0;

          return Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: isLow ? AppTheme.dangerColor.withOpacity(0.3) : AppTheme.borderColor),
            ),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: (isLow ? AppTheme.dangerColor : AppTheme.primaryColor).withOpacity(0.1),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Icon(Icons.inventory_2, color: isLow ? AppTheme.dangerColor : AppTheme.primaryColor, size: 20),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(p['name'] ?? 'Product', style: GoogleFonts.inter(fontSize: 13, fontWeight: FontWeight.w600)),
                      const SizedBox(height: 2),
                      Text('SKU: ${p['sku'] ?? 'N/A'} • ${AppHelpers.formatMoney(p['price'] ?? 0)}', style: GoogleFonts.inter(fontSize: 11, color: AppTheme.textSecondary)),
                    ],
                  ),
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text('$stock', style: GoogleFonts.inter(fontSize: 16, fontWeight: FontWeight.w800, color: isLow ? AppTheme.dangerColor : AppTheme.textPrimary)),
                    Text('in stock', style: GoogleFonts.inter(fontSize: 10, color: AppTheme.textMuted)),
                  ],
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
