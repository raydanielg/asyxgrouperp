class AppConstants {
  static const String appName = 'AsyxGroup ERP';
  static const String appVersion = '1.0.0';
  static const String baseUrl = 'https://your-domain.com/api';
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String biometricKey = 'biometric_enabled';
  static const String onboardingKey = 'onboarding_complete';
  static const String themeKey = 'app_theme';
  static const String companyKey = 'active_company';

  static const int connectTimeout = 30000;
  static const int receiveTimeout = 30000;

  static const List<String> roles = [
    'admin',
    'administrator',
    'admin_manager',
    'director',
    'finance_officer',
    'auditor',
    'hr_officer',
    'legal_officer',
    'receptionist',
    'logistics_officer',
    'technical_manager',
    'technician',
    'ict_officer',
    'ict_engineer',
    'project_manager',
    'operations_manager',
    'call_center_agent',
    'cashier',
    'supervisor',
  ];
}
