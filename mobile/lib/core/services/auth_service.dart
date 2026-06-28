import 'dart:convert';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:local_auth/local_auth.dart';
import '../constants.dart';
import '../models/user_model.dart';
import 'api_service.dart';

class AuthService {
  final ApiService _api = ApiService();
  final FlutterSecureStorage _storage = const FlutterSecureStorage();
  final LocalAuthentication _localAuth = LocalAuthentication();

  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await _api.post('/login', data: {
      'email': email,
      'password': password,
    });

    final data = response.data;
    await _api.setToken(data['token']);
    await _storage.write(key: AppConstants.userKey, value: jsonEncode(data['user']));

    return data;
  }

  Future<void> logout() async {
    try {
      await _api.post('/logout');
    } catch (_) {}
    await _api.clearToken();
  }

  Future<UserModel?> getCurrentUser() async {
    final userJson = await _storage.read(key: AppConstants.userKey);
    if (userJson != null) {
      return UserModel.fromJson(jsonDecode(userJson));
    }
    return null;
  }

  Future<UserModel> refreshUser() async {
    final response = await _api.get('/user');
    await _storage.write(key: AppConstants.userKey, value: jsonEncode(response.data));
    return UserModel.fromJson(response.data);
  }

  Future<bool> isAuthenticated() async {
    final token = await _api.getToken();
    return token != null;
  }

  Future<bool> isBiometricEnabled() async {
    final value = await _storage.read(key: AppConstants.biometricKey);
    return value == 'true';
  }

  Future<void> setBiometricEnabled(bool enabled) async {
    await _storage.write(key: AppConstants.biometricKey, value: enabled.toString());
  }

  Future<bool> authenticateWithBiometric() async {
    try {
      final canAuth = await _localAuth.canCheckBiometrics;
      if (!canAuth) return false;

      return await _localAuth.authenticate(
        localizedReason: 'Authenticate to access AsyxGroup ERP',
        options: const AuthenticationOptions(
          stickyAuth: true,
          biometricOnly: true,
        ),
      );
    } catch (_) {
      return false;
    }
  }

  Future<bool> canUseBiometric() async {
    try {
      return await _localAuth.canCheckBiometrics;
    } catch (_) {
      return false;
    }
  }

  Future<bool> isOnboardingComplete() async {
    final value = await _storage.read(key: AppConstants.onboardingKey);
    return value == 'true';
  }

  Future<void> setOnboardingComplete() async {
    await _storage.write(key: AppConstants.onboardingKey, value: 'true');
  }
}
