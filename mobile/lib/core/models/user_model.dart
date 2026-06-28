class UserModel {
  final int id;
  final String name;
  final String email;
  final String? role;
  final int? companyId;
  final String? companyName;
  final String? phone;
  final String? avatar;
  final List<RoleModel> roles;
  final DateTime? createdAt;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    this.role,
    this.companyId,
    this.companyName,
    this.phone,
    this.avatar,
    this.roles = const [],
    this.createdAt,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      role: json['role'],
      companyId: json['company_id'],
      companyName: json['company']?['name'],
      phone: json['phone'],
      avatar: json['avatar'],
      roles: (json['roles'] as List?)?.map((r) => RoleModel.fromJson(r)).toList() ?? [],
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at']) : null,
    );
  }

  String get primaryRole {
    if (roles.isNotEmpty) return roles.first.name;
    return role ?? 'user';
  }

  String get roleLabel {
    final labels = {
      'admin': 'Administrator',
      'director': 'Director',
      'admin_manager': 'Admin Manager',
      'administrator': 'Administrator',
      'finance_officer': 'Finance Officer',
      'auditor': 'Auditor',
      'hr_officer': 'HR Officer',
      'legal_officer': 'Legal Officer',
      'receptionist': 'Receptionist',
      'logistics_officer': 'Logistics Officer',
      'technical_manager': 'Technical Manager',
      'technician': 'Technician',
      'ict_officer': 'ICT Officer',
      'project_manager': 'Project Manager',
      'operations_manager': 'Operations Manager',
      'call_center_agent': 'Call Center Agent',
      'cashier': 'Cashier',
      'supervisor': 'Supervisor',
      'ict_engineer': 'ICT Engineer',
    };
    return labels[primaryRole] ?? primaryRole.replaceAll('_', ' ').split(' ').map((w) => w.isNotEmpty ? '${w[0].toUpperCase()}${w.substring(1)}' : '').join(' ');
  }

  Map<String, dynamic> toJson() => {
    'id': id,
    'name': name,
    'email': email,
    'role': role,
    'company_id': companyId,
    'phone': phone,
    'avatar': avatar,
    'roles': roles.map((r) => r.toJson()).toList(),
  };
}

class RoleModel {
  final int id;
  final String name;
  final String? guardName;

  RoleModel({required this.id, required this.name, this.guardName});

  factory RoleModel.fromJson(Map<String, dynamic> json) {
    return RoleModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      guardName: json['guard_name'],
    );
  }

  Map<String, dynamic> toJson() => {'id': id, 'name': name, 'guard_name': guardName};
}
