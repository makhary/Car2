# Plan technique plateforme de gestion automobile

## 1) Synthèse courte
- Entités clés : users, roles, structures, vehicles, vehicle_assignments, maintenances, fuel_logs, insurances, technical_inspections, documents, alerts, notifications.
- Relations : users appartiennent optionnellement à une structure (belongsTo) et portent plusieurs rôles (many-to-many). Structures possèdent véhicules et utilisateurs (hasMany). Vehicles liés aux maintenances, fuel_logs, insurances, technical_inspections, documents, alerts (hasMany) et aux conducteurs via vehicle_assignments (many-to-many avec métadonnées). Maintenances et fuel_logs sont reliés à un rapporteur/conducteur (belongsTo user). Documents peuvent appartenir à un véhicule ou une maintenance et sont uploadés par un user.
- Rôles : Admin (plein accès), Gestionnaire de flotte (gestion des ressources de sa structure), Conducteur (consultation véhicules assignés, saisie pleins/incidents), Mécanicien (saisie interventions, optionnel).
- Modules majeurs : Auth/RBAC, gestion véhicules et affectations, suivi carburant, maintenance/réparations, assurances/visites techniques, documents, alertes/notifications, tableau de bord et statistiques.
- Sécurité : Sanctum pour l’auth, policies par ressource, gates transverses (manage-fleet).
- Notifications : emails + in-app Laravel, push mobile via FCM.
- MVP : Auth + rôles, CRUD véhicules, fuel logs, maintenances basiques, fiche véhicule avec onglets.
- Phase 2+ : assurances, visites, documents, alertes, dashboard, exports, mobile avancé.

## 2) Plan de génération du code
1. Migrations Laravel : users/roles/structures, vehicles et assignments, maintenances/fuel_logs, assurances/visites, documents/alerts.
2. Modèles Eloquent + relations.
3. Seeders de base : rôles + admin.
4. Authentification Sanctum + routes auth (login/register/me/logout).
5. Routes API v1 (apiResource + endpoints spécifiques).
6. Contrôleurs API CRUD principaux + affectation véhicule, dashboard.
7. Policies + middleware rôles/gates (manage-fleet, etc.).
8. Frontend web (Blade/Livewire) : pages dashboard, véhicules, pleins, entretiens, documents, utilisateurs, alertes.
9. Application mobile (Flutter ou React Native) : structure dossiers, service API, écrans conducteur clés.

## 3) Migrations Laravel

```php
// 2024_01_01_000000_create_structures_table.php
Schema::create('structures', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('address')->nullable();
    $table->timestamps();
});

// 2024_01_01_000100_create_users_table.php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->foreignId('structure_id')->nullable()->constrained()->nullOnDelete();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});

// 2024_01_01_000200_create_roles_and_pivot.php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->enum('name', ['admin', 'fleet_manager', 'driver', 'mechanic']);
    $table->string('guard_name')->default('web');
    $table->timestamps();
});

Schema::create('role_user', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
    $table->primary(['user_id', 'role_id']);
});

// 2024_01_01_000300_create_vehicles_table.php
Schema::create('vehicles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('structure_id')->nullable()->constrained()->nullOnDelete();
    $table->string('registration')->unique();
    $table->string('brand');
    $table->string('model');
    $table->year('year')->nullable();
    $table->string('vin')->nullable();
    $table->enum('status', ['active', 'archived'])->default('active');
    $table->unsignedInteger('mileage')->default(0);
    $table->string('fuel_type')->nullable();
    $table->date('purchase_date')->nullable();
    $table->timestamp('archived_at')->nullable();
    $table->timestamps();
});

// 2024_01_01_000400_create_vehicle_assignments_table.php
Schema::create('vehicle_assignments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->date('start_date');
    $table->date('end_date')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});

// 2024_01_01_000500_create_maintenances_table.php
Schema::create('maintenances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
    $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
    $table->enum('type', ['maintenance', 'repair']);
    $table->string('title');
    $table->text('description')->nullable();
    $table->enum('status', ['open', 'in_progress', 'done', 'canceled'])->default('open');
    $table->decimal('cost', 10, 2)->default(0);
    $table->unsignedInteger('mileage')->nullable();
    $table->date('performed_at')->nullable();
    $table->date('next_due_at')->nullable();
    $table->foreignId('garage_id')->nullable()->constrained('users')->nullOnDelete();
    $table->json('attachments')->nullable();
    $table->timestamps();
});

// 2024_01_01_000600_create_fuel_logs_table.php
Schema::create('fuel_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->date('filled_at');
    $table->string('station')->nullable();
    $table->decimal('liters', 8, 2);
    $table->decimal('total_cost', 10, 2);
    $table->unsignedInteger('odometer');
    $table->string('fuel_type')->nullable();
    $table->string('receipt_path')->nullable();
    $table->timestamps();
});

// 2024_01_01_000700_create_insurances_table.php
Schema::create('insurances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
    $table->string('provider');
    $table->string('policy_number');
    $table->date('start_date');
    $table->date('end_date');
    $table->decimal('cost', 10, 2)->nullable();
    $table->string('document_path')->nullable();
    $table->timestamps();
});

// 2024_01_01_000800_create_technical_inspections_table.php
Schema::create('technical_inspections', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
    $table->date('performed_at');
    $table->date('next_due_at')->nullable();
    $table->string('center')->nullable();
    $table->string('result')->nullable();
    $table->string('document_path')->nullable();
    $table->timestamps();
});

// 2024_01_01_000900_create_documents_table.php
Schema::create('documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('maintenance_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
    $table->string('path');
    $table->string('type');
    $table->string('label')->nullable();
    $table->string('mime')->nullable();
    $table->unsignedBigInteger('size')->nullable();
    $table->timestamps();
});

// 2024_01_01_001000_create_alerts_table.php
Schema::create('alerts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
    $table->string('type');
    $table->text('message');
    $table->date('due_date')->nullable();
    $table->timestamp('resolved_at')->nullable();
    $table->timestamps();
});
```

## 4) Modèles Eloquent
```php
class Structure extends Model {
    protected $fillable = ['name','address'];
    public function users(){ return $this->hasMany(User::class); }
    public function vehicles(){ return $this->hasMany(Vehicle::class); }
}

class Role extends Model {
    protected $fillable = ['name','guard_name'];
    public function users(){ return $this->belongsToMany(User::class); }
}

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = ['name','email','password','structure_id'];
    protected $hidden = ['password','remember_token'];
    public function structure(){ return $this->belongsTo(Structure::class); }
    public function roles(){ return $this->belongsToMany(Role::class); }
    public function vehicleAssignments(){ return $this->hasMany(VehicleAssignment::class); }
    public function fuelLogs(){ return $this->hasMany(FuelLog::class); }
    public function maintenancesReported(){ return $this->hasMany(Maintenance::class, 'reported_by'); }
    public function documentsUploaded(){ return $this->hasMany(Document::class, 'uploaded_by'); }
    public function hasRole(...$roles){ return $this->roles()->whereIn('name', $roles)->exists(); }
}

class Vehicle extends Model {
    protected $fillable = ['structure_id','registration','brand','model','year','vin','status','mileage','fuel_type','purchase_date','archived_at'];
    public function structure(){ return $this->belongsTo(Structure::class); }
    public function assignments(){ return $this->hasMany(VehicleAssignment::class); }
    public function maintenances(){ return $this->hasMany(Maintenance::class); }
    public function fuelLogs(){ return $this->hasMany(FuelLog::class); }
    public function insurances(){ return $this->hasMany(Insurance::class); }
    public function technicalInspections(){ return $this->hasMany(TechnicalInspection::class); }
    public function documents(){ return $this->hasMany(Document::class); }
    public function alerts(){ return $this->hasMany(Alert::class); }
}

class VehicleAssignment extends Model {
    protected $fillable = ['vehicle_id','user_id','start_date','end_date','notes'];
    public function vehicle(){ return $this->belongsTo(Vehicle::class); }
    public function user(){ return $this->belongsTo(User::class); }
}

class Maintenance extends Model {
    protected $fillable = ['vehicle_id','reported_by','type','title','description','status','cost','mileage','performed_at','next_due_at','garage_id','attachments'];
    protected $casts = ['attachments' => 'array'];
    public function vehicle(){ return $this->belongsTo(Vehicle::class); }
    public function reporter(){ return $this->belongsTo(User::class, 'reported_by'); }
    public function garage(){ return $this->belongsTo(User::class, 'garage_id'); }
    public function documents(){ return $this->hasMany(Document::class); }
}

class FuelLog extends Model {
    protected $fillable = ['vehicle_id','user_id','filled_at','station','liters','total_cost','odometer','fuel_type','receipt_path'];
    public function vehicle(){ return $this->belongsTo(Vehicle::class); }
    public function user(){ return $this->belongsTo(User::class); }
}

class Insurance extends Model {
    protected $fillable = ['vehicle_id','provider','policy_number','start_date','end_date','cost','document_path'];
    public function vehicle(){ return $this->belongsTo(Vehicle::class); }
}

class TechnicalInspection extends Model {
    protected $fillable = ['vehicle_id','performed_at','next_due_at','center','result','document_path'];
    public function vehicle(){ return $this->belongsTo(Vehicle::class); }
}

class Document extends Model {
    protected $fillable = ['vehicle_id','maintenance_id','uploaded_by','path','type','label','mime','size'];
    public function vehicle(){ return $this->belongsTo(Vehicle::class); }
    public function maintenance(){ return $this->belongsTo(Maintenance::class); }
    public function uploader(){ return $this->belongsTo(User::class, 'uploaded_by'); }
}

class Alert extends Model {
    protected $fillable = ['vehicle_id','type','message','due_date','resolved_at'];
    protected $dates = ['due_date','resolved_at'];
    public function vehicle(){ return $this->belongsTo(Vehicle::class); }
}
```

## 5) Seeders de base
```php
class RoleSeeder extends Seeder {
    public function run(){
        foreach (['admin','fleet_manager','driver','mechanic'] as $role) {
            Role::firstOrCreate(['name' => $role], ['guard_name' => 'web']);
        }
    }
}

class AdminUserSeeder extends Seeder {
    public function run(){
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->roles()->sync([Role::where('name','admin')->first()->id]);
    }
}
```

## 6) Routes API & contrôleurs
```php
Route::prefix('v1')->group(function(){
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function(){
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::apiResource('vehicles', VehicleController::class);
        Route::apiResource('maintenances', MaintenanceController::class);
        Route::apiResource('fuel-logs', FuelLogController::class)->only(['index','store','show']);
        Route::apiResource('insurances', InsuranceController::class);
        Route::apiResource('technical-inspections', TechnicalInspectionController::class);
        Route::apiResource('documents', DocumentController::class)->only(['store','show','destroy']);
        Route::apiResource('alerts', AlertController::class)->only(['index','update']);
        Route::post('vehicles/{vehicle}/assign', [VehicleAssignmentController::class, 'store']);
        Route::get('dashboard', [DashboardController::class, '__invoke']);
    });
});
```

Contrôleurs : méthodes CRUD standard avec `authorizeResource` et validations. Exemple pour Vehicles et FuelLogs :

```php
class VehicleController extends Controller {
    public function __construct(){ $this->authorizeResource(Vehicle::class, 'vehicle'); }

    public function index(){ return Vehicle::with(['structure','assignments.user'])->paginate(); }
    public function store(Request $request){
        $data = $request->validate([
            'structure_id' => 'nullable|exists:structures,id',
            'registration' => 'required|string|unique:vehicles',
            'brand' => 'required|string',
            'model' => 'required|string',
            'year' => 'nullable|digits:4',
            'vin' => 'nullable|string',
            'status' => 'in:active,archived',
            'mileage' => 'nullable|integer',
            'fuel_type' => 'nullable|string',
            'purchase_date' => 'nullable|date',
        ]);
        $vehicle = Vehicle::create($data);
        return response()->json($vehicle, 201);
    }
    public function show(Vehicle $vehicle){ return $vehicle->load(['maintenances','fuelLogs','documents']); }
    public function update(Request $request, Vehicle $vehicle){
        $data = $request->validate([
            'registration' => 'sometimes|string|unique:vehicles,registration,'.$vehicle->id,
            'brand' => 'sometimes|string',
            'model' => 'sometimes|string',
            'status' => 'in:active,archived',
            'mileage' => 'sometimes|integer',
            'archived_at' => 'nullable|date',
        ]);
        $vehicle->update($data);
        return $vehicle->refresh();
    }
    public function destroy(Vehicle $vehicle){ $vehicle->delete(); return response()->noContent(); }
}

class FuelLogController extends Controller {
    public function __construct(){ $this->authorizeResource(FuelLog::class, 'fuel_log'); }

    public function index(){ return FuelLog::with(['vehicle','user'])->latest('filled_at')->paginate(); }
    public function store(Request $request){
        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'filled_at' => 'required|date',
            'station' => 'nullable|string',
            'liters' => 'required|numeric|min:0',
            'total_cost' => 'required|numeric|min:0',
            'odometer' => 'required|integer|min:0',
            'fuel_type' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,png',
        ]);
        if($request->hasFile('receipt')){
            $data['receipt_path'] = $request->file('receipt')->store('receipts');
        }
        $data['user_id'] = $request->user()->id;
        $fuelLog = FuelLog::create($data);
        return response()->json($fuelLog, 201);
    }
    public function show(FuelLog $fuel_log){ return $fuel_log->load(['vehicle','user']); }
}

class VehicleAssignmentController extends Controller {
    public function store(Request $request, Vehicle $vehicle){
        $this->authorize('update', $vehicle);
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string'
        ]);
        $assignment = $vehicle->assignments()->create($data);
        return response()->json($assignment, 201);
    }
}
```

## 7) Authentification & rôles
- Sanctum pour tokens personnels (`composer require laravel/sanctum`).
- Routes auth ci-dessus; contrôleur AuthController : register/login créent un token, me retourne user + rôles, logout révoque tokens (`$request->user()->currentAccessToken()->delete()`).
- Middleware `auth:sanctum` sur les routes protégées.
- Policies par ressource (VehiclePolicy, FuelLogPolicy, MaintenancePolicy…) évaluant rôle et structure :
```php
public function update(User $user, Vehicle $vehicle){
    return $user->hasRole('admin','fleet_manager') && ($user->structure_id === null || $user->structure_id === $vehicle->structure_id);
}
```
- Gates transverses dans AuthServiceProvider : `Gate::define('manage-fleet', fn(User $u) => $u->hasRole('admin','fleet_manager'));` puis middleware `can:manage-fleet`.

## 8) Frontend web (Blade/Livewire)
- Pages clés :
  - Login/Register.
  - Dashboard (KPIs, alertes ouvertes, coûts récents).
  - Véhicules (liste + filtres statut) ; fiche véhicule avec onglets Infos / Entretiens / Carburant / Documents / Conducteurs.
  - Plein carburant (form modal Livewire ou page). 
  - Entretien/Incident (form avec type, date, coût, garage, pièces jointes).
  - Assurances/visites techniques.
  - Gestion utilisateurs/ rôles, configuration alertes.
- Exemple Blade (fiche véhicule) :
```php
@extends('layouts.app')
@section('content')
<div class="container">
  <h1>{{ $vehicle->registration }} - {{ $vehicle->brand }} {{ $vehicle->model }}</h1>
  @livewire('vehicle-tabs', ['vehicle' => $vehicle->id])
</div>
@endsection
```
- Livewire component `VehicleTabs` appelle API `/api/v1/vehicles/{id}` puis rend onglets; formulaires appellent les endpoints fuel-logs, maintenances, documents.

## 9) Application mobile (Flutter ou React Native)
- Structure dossiers (Flutter) :
  - `lib/` → `core/` (config, constants), `services/api_service.dart`, `features/auth/`, `features/vehicles/`, `features/fuel/`, `features/maintenance/`.
  - Stockage token : `flutter_secure_storage` ; interceptor Dio ajoute header `Authorization: Bearer <token>`.
- Flux : écran Login → appel POST `/api/v1/auth/login` → stock token → navigation vers liste véhicules affectés (`GET /api/v1/vehicles?assigned=me`).
- Exemple pseudo-code Dart :
```dart
final response = await api.post('/api/v1/fuel-logs', data: {
  'vehicle_id': vehicleId,
  'filled_at': DateTime.now().toIso8601String(),
  'liters': liters,
  'total_cost': cost,
  'odometer': odometer,
});
```
- RN équivalent : dossier `services/api.ts`, stockage token dans `AsyncStorage` + Axios interceptor.
```
