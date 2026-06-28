import 'package:flutter/material.dart';
import '../models/user_model.dart';
import '../services/auth_service.dart';
import '../services/api_service.dart';

enum AuthStatus { uninitialized, authenticated, unauthenticated, loading }

class AuthProvider with ChangeNotifier {
  final AuthService _authService = AuthService();

  AuthStatus _status = AuthStatus.uninitialized;
  UserModel? _user;
  String? _error;
  Map<String, dynamic>? _dashboardData;

  AuthStatus get status => _status;
  UserModel? get user => _user;
  String? get error => _error;
  Map<String, dynamic>? get dashboardData => _dashboardData;
  bool get isAuthenticated => _status == AuthStatus.authenticated;

  Future<void> init() async {
    final isAuth = await _authService.isAuthenticated();
    if (isAuth) {
      _user = await _authService.getCurrentUser();
      if (_user != null) {
        _status = AuthStatus.authenticated;
      } else {
        _status = AuthStatus.unauthenticated;
      }
    } else {
      _status = AuthStatus.unauthenticated;
    }
    notifyListeners();
  }

  Future<bool> login(String email, String password) async {
    _status = AuthStatus.loading;
    _error = null;
    notifyListeners();

    try {
      final data = await _authService.login(email, password);
      _user = UserModel.fromJson(data['user']);
      _status = AuthStatus.authenticated;
      notifyListeners();
      return true;
    } catch (e) {
      _error = _parseError(e);
      _status = AuthStatus.unauthenticated;
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    await _authService.logout();
    _user = null;
    _dashboardData = null;
    _status = AuthStatus.unauthenticated;
    notifyListeners();
  }

  Future<void> refreshUser() async {
    try {
      _user = await _authService.refreshUser();
      notifyListeners();
    } catch (_) {}
  }

  Future<void> loadDashboard() async {
    try {
      final response = await ApiService().get('/dashboard/role');
      _dashboardData = response.data;
      notifyListeners();
    } catch (_) {}
  }

  Future<bool> authenticateWithBiometric() async {
    return await _authService.authenticateWithBiometric();
  }

  Future<bool> canUseBiometric() async {
    return await _authService.canUseBiometric();
  }

  Future<bool> isBiometricEnabled() async {
    return await _authService.isBiometricEnabled();
  }

  Future<void> setBiometricEnabled(bool enabled) async {
    await _authService.setBiometricEnabled(enabled);
  }

  Future<bool> isOnboardingComplete() async {
    return await _authService.isOnboardingComplete();
  }

  Future<void> setOnboardingComplete() async {
    await _authService.setOnboardingComplete();
  }

  String _parseError(dynamic e) {
    if (e is Exception) {
      final str = e.toString();
      if (str.contains('422')) return 'Invalid email or password';
      if (str.contains('401')) return 'Unauthorized access';
      if (str.contains('500')) return 'Server error. Please try again later.';
      if (str.contains('SocketException') || str.contains('connection')) {
        return 'No internet connection';
      }
    }
    return 'Login failed. Please try again.';
  }
}
