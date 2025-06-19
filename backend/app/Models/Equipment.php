<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipment';
    protected $primaryKey = 'resource_id';

    protected $fillable = [
        'equipment_name',
        'description',
        'brand',
        'location_note',
        'category_id',
        'subcategory_id',
        'department_id',
        'total_quantity',
        'rental_fee',
        'company_fee',
        'status_id',
        'rate_type',
        'minimum_hour',
        'created_by',
        'updated_by',
        'deleted_by',
        'last_booked_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'last_booked_at'
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(EquipmentCategories::class, 'category_id', 'category_id');
    }

    public function department()
    {
        return $this->belongsTo(Departments::class, 'department_id', 'department_id');
    }

    public function rateType()
    {
        return $this->belongsTo(RateTypes::class, 'rate_type', 'rate_type_id');
    }

    public function equipmentItems()
    {
        return $this->hasMany(EquipmentItems::class, 'resource_id', 'resource_id');
    }

    public function equipmentImages()
    {
        return $this->hasMany(EquipmentImages::class, 'equipment_id', 'resource_id');
    }

    public function createdByAdmin()
    {
        return $this->belongsTo(Admins::class, 'created_by', 'admin_id');
    }

    public function updatedByAdmin()
    {
        return $this->belongsTo(Admins::class, 'updated_by', 'admin_id');
    }

    // Scopes
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }
}

// app/Models/Department.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';
    protected $primaryKey = 'department_id';

    protected $fillable = [
        'department_name',
        'department_description'
    ];

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'department_id', 'department_id');
    }

    public function departmentAdmins()
    {
        return $this->hasMany(DepartmentAdmin::class, 'department_id', 'department_id');
    }
}

// app/Models/Admin.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'admins';
    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'photo_url',
        'first_name',
        'last_name',
        'middle_name',
        'role_id',
        'school_id',
        'email',
        'contact_number',
        'hashed_password'
    ];

    protected $hidden = [
        'hashed_password'
    ];

    public function role()
    {
        return $this->belongsTo(AdminRole::class, 'role_id', 'role_id');
    }

    public function departmentAdmins()
    {
        return $this->hasMany(DepartmentAdmin::class, 'admin_id', 'admin_id');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_admins', 'admin_id', 'department_id', 'admin_id', 'department_id');
    }

    // Check if admin has inventory manager role
    public function isInventoryManager()
    {
        return $this->role && $this->role->role_name === 'Inventory Manager';
    }

    // Get departments that this admin manages
    public function getManagedDepartments()
    {
        return $this->departments;
    }
}

// app/Models/DepartmentAdmin.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentAdmin extends Model
{
    use HasFactory;

    protected $table = 'department_admins';
    protected $primaryKey = 'dept_admin_id';

    protected $fillable = [
        'department_id',
        'admin_id'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }
}

// app/Models/AdminRole.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    use HasFactory;

    protected $table = 'admin_roles';
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role_name',
        'role_description'
    ];

    public function admins()
    {
        return $this->hasMany(Admin::class, 'role_id', 'role_id');
    }
}