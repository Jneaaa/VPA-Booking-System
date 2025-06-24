namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    /**
     * Return a list of departments.
     */
    public function index(): JsonResponse
    {
        try {
            $departments = Department::all(['department_id', 'department_name']);
            return response()->json($departments);
        } catch (\Exception $e) {
            \Log::error('Error fetching departments', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch departments'], 500);
        }
    }
}
