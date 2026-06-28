import 'package:intl/intl.dart';
import 'package:flutter/material.dart';

class AppHelpers {
  static String formatMoney(dynamic amount, {String currency = 'TZS'}) {
    final num value = amount is num ? amount : num.tryParse(amount.toString()) ?? 0;
    final formatter = NumberFormat('#,##0', 'en_US');
    return '$currency ${formatter.format(value)}';
  }

  static String formatDate(String? dateStr, {String format = 'dd MMM yyyy'}) {
    if (dateStr == null || dateStr.isEmpty) return '-';
    try {
      final date = DateTime.parse(dateStr);
      return DateFormat(format).format(date);
    } catch (_) {
      return dateStr;
    }
  }

  static String formatDateTime(String? dateStr) {
    if (dateStr == null || dateStr.isEmpty) return '-';
    try {
      final date = DateTime.parse(dateStr);
      return DateFormat('dd MMM yyyy HH:mm').format(date);
    } catch (_) {
      return dateStr;
    }
  }

  static String timeAgo(String? dateStr) {
    if (dateStr == null || dateStr.isEmpty) return '';
    try {
      final date = DateTime.parse(dateStr);
      final diff = DateTime.now().difference(date);
      if (diff.inDays > 365) return '${diff.inDays ~/ 365}y ago';
      if (diff.inDays > 30) return '${diff.inDays ~/ 30}mo ago';
      if (diff.inDays > 0) return '${diff.inDays}d ago';
      if (diff.inHours > 0) return '${diff.inHours}h ago';
      if (diff.inMinutes > 0) return '${diff.inMinutes}m ago';
      return 'Just now';
    } catch (_) {
      return '';
    }
  }

  static Color statusColor(String? status) {
    switch (status?.toLowerCase()) {
      case 'active':
      case 'completed':
      case 'approved':
      case 'present':
      case 'paid':
      case 'resolved':
        return const Color(0xFF10B981);
      case 'pending':
      case 'draft':
      case 'in_progress':
      case 'in progress':
      case 'late':
        return const Color(0xFFF59E0B);
      case 'rejected':
      case 'cancelled':
      case 'overdue':
      case 'absent':
      case 'failed':
        return const Color(0xFFEF4444);
      case 'open':
      case 'new':
      case 'qualified':
        return const Color(0xFF3B82F6);
      default:
        return const Color(0xFF64748B);
    }
  }

  static IconData statusIcon(String? status) {
    switch (status?.toLowerCase()) {
      case 'active':
      case 'completed':
      case 'approved':
      case 'present':
      case 'resolved':
        return Icons.check_circle;
      case 'pending':
      case 'draft':
        return Icons.pending;
      case 'in_progress':
      case 'in progress':
        return Icons.autorenew;
      case 'rejected':
      case 'cancelled':
      case 'overdue':
      case 'absent':
        return Icons.cancel;
      case 'open':
      case 'new':
        return Icons.fiber_new;
      default:
        return Icons.info;
    }
  }

  static String abbreviate(String text, {int maxLength = 20}) {
    if (text.length <= maxLength) return text;
    return '${text.substring(0, maxLength)}...';
  }

  static String initials(String name) {
    final parts = name.trim().split(' ');
    if (parts.length >= 2) {
      return '${parts[0][0]}${parts[1][0]}'.toUpperCase();
    }
    return name.isNotEmpty ? name[0].toUpperCase() : '?';
  }
}
