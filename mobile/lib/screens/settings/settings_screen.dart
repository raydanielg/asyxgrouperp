import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../../core/theme.dart';
import '../../core/providers/auth_provider.dart';
import '../../core/utils/helpers.dart';

class SettingsScreen extends StatefulWidget {
  const SettingsScreen({super.key});

  @override
  State<SettingsScreen> createState() => _SettingsScreenState();
}

class _SettingsScreenState extends State<SettingsScreen> {
  bool _biometricEnabled = false;
  bool _notificationsEnabled = true;

  @override
  void initState() {
    super.initState();
    _loadSettings();
  }

  void _loadSettings() async {
    final bio = await context.read<AuthProvider>().isBiometricEnabled();
    setState(() => _biometricEnabled = bio);
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    return Scaffold(
      appBar: AppBar(title: const Text('Settings')),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Profile Section
            Container(
              margin: const EdgeInsets.all(16),
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: const LinearGradient(colors: [AppTheme.primaryDark, AppTheme.primaryColor]),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Row(
                children: [
                  CircleAvatar(
                    radius: 30,
                    backgroundColor: Colors.white.withOpacity(0.2),
                    child: Text(
                      AppHelpers.initials(user?.name ?? 'U'),
                      style: GoogleFonts.inter(fontSize: 20, fontWeight: FontWeight.w700, color: Colors.white),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(user?.name ?? 'User', style: GoogleFonts.inter(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.white)),
                        const SizedBox(height: 2),
                        Text(user?.email ?? '', style: GoogleFonts.inter(fontSize: 12, color: Colors.white70)),
                        const SizedBox(height: 2),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                          decoration: BoxDecoration(color: Colors.white.withOpacity(0.2), borderRadius: BorderRadius.circular(6)),
                          child: Text(user?.roleLabel ?? '', style: GoogleFonts.inter(fontSize: 10, fontWeight: FontWeight.w600, color: Colors.white)),
                        ),
                      ],
                    ),
                  ),
                  IconButton(icon: const Icon(Icons.edit, color: Colors.white, size: 20), onPressed: () {}),
                ],
              ),
            ),

            // Security Section
            _sectionHeader('Security'),
            _settingTile(
              icon: Icons.fingerprint,
              title: 'Biometric Login',
              subtitle: 'Use fingerprint or face to login',
              trailing: Switch(
                value: _biometricEnabled,
                activeColor: AppTheme.primaryColor,
                onChanged: (v) async {
                  await context.read<AuthProvider>().setBiometricEnabled(v);
                  setState(() => _biometricEnabled = v);
                },
              ),
            ),
            _settingTile(icon: Icons.lock, title: 'Change Password', subtitle: 'Update your login password', onTap: () {}),
            _settingTile(icon: Icons.devices, title: 'Active Sessions', subtitle: 'Manage your login sessions', onTap: () {}),
            _settingTile(icon: Icons.shield, title: 'Two-Factor Auth', subtitle: 'Add extra security layer', onTap: () {}),

            // Preferences Section
            _sectionHeader('Preferences'),
            _settingTile(
              icon: Icons.notifications,
              title: 'Push Notifications',
              subtitle: 'Receive real-time alerts',
              trailing: Switch(
                value: _notificationsEnabled,
                activeColor: AppTheme.primaryColor,
                onChanged: (v) => setState(() => _notificationsEnabled = v),
              ),
            ),
            _settingTile(icon: Icons.language, title: 'Language', subtitle: 'English', onTap: () {}),
            _settingTile(icon: Icons.dark_mode, title: 'Dark Mode', subtitle: 'Coming soon', onTap: () {}),

            // App Section
            _sectionHeader('App'),
            _settingTile(icon: Icons.info_outline, title: 'About', subtitle: 'Version 1.0.0', onTap: () {}),
            _settingTile(icon: Icons.help_outline, title: 'Help & Support', subtitle: 'Get assistance', onTap: () {}),
            _settingTile(icon: Icons.privacy_tip_outlined, title: 'Privacy Policy', subtitle: 'View our policies', onTap: () {}),

            // Logout
            Padding(
              padding: const EdgeInsets.all(16),
              child: SizedBox(
                width: double.infinity,
                child: OutlinedButton.icon(
                  onPressed: () => _logout(context),
                  icon: const Icon(Icons.logout, color: AppTheme.dangerColor),
                  label: Text('Sign Out', style: GoogleFonts.inter(fontWeight: FontWeight.w600, color: AppTheme.dangerColor)),
                  style: OutlinedButton.styleFrom(
                    side: const BorderSide(color: AppTheme.dangerColor),
                    padding: const EdgeInsets.symmetric(vertical: 14),
                  ),
                ),
              ),
            ),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }

  Widget _sectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 4),
      child: Align(
        alignment: Alignment.centerLeft,
        child: Text(title, style: GoogleFonts.inter(fontSize: 12, fontWeight: FontWeight.w700, color: AppTheme.textMuted, letterSpacing: 1)),
      ),
    );
  }

  Widget _settingTile({required IconData icon, required String title, String? subtitle, Widget? trailing, VoidCallback? onTap}) {
    return ListTile(
      leading: Container(
        padding: const EdgeInsets.all(8),
        decoration: BoxDecoration(color: AppTheme.primaryColor.withOpacity(0.1), borderRadius: BorderRadius.circular(8)),
        child: Icon(icon, color: AppTheme.primaryColor, size: 20),
      ),
      title: Text(title, style: GoogleFonts.inter(fontSize: 14, fontWeight: FontWeight.w600)),
      subtitle: subtitle != null ? Text(subtitle, style: GoogleFonts.inter(fontSize: 12, color: AppTheme.textSecondary)) : null,
      trailing: trailing ?? (onTap != null ? const Icon(Icons.chevron_right, size: 20, color: AppTheme.textMuted) : null),
      onTap: onTap,
    );
  }

  void _logout(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text('Sign Out', style: GoogleFonts.inter(fontWeight: FontWeight.w700)),
        content: const Text('Are you sure you want to sign out?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(ctx);
              await context.read<AuthProvider>().logout();
              if (mounted) Navigator.of(context).pushNamedAndRemoveUntil('/login', (route) => false);
            },
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.dangerColor),
            child: const Text('Sign Out'),
          ),
        ],
      ),
    );
  }
}
