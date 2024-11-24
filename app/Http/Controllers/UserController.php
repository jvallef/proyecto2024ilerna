<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserDeletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    protected $userDeletionService;
    private $currentPage;

    public function __construct(UserDeletionService $userDeletionService)
    {
        $this->userDeletionService = $userDeletionService;
        
        // Solo admin puede crear, editar y eliminar usuarios
        $this->middleware(['role:admin'])->only(['create', 'store', 'edit', 'update', 'destroy', 'forceDelete', 'restore']);
        
        // Teachers pueden ver listados de estudiantes y sus perfiles
        $this->middleware(['role:admin|teacher'])->only(['index', 'show']);
    }

    /**
     * Aplica los filtros de búsqueda al query de usuarios
     */
    private function applyUserFilters($query, $search = null)
    {
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Muestra un listado de usuarios según el rol del usuario autenticado.
     */
    public function index(Request $request)
    {
        $query = User::query();
        $query = $this->applyUserFilters($query, $request->input('search'));
        $users = $query->paginate(config('app.pagination.per_page', 10))->withQueryString();

        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            $users = $users;
        } elseif ($user->hasRole('teacher')) {
            // Solo estudiantes de los cursos que imparte
            $users = User::role('student')
                ->whereHas('enrolledCourses', function ($query) use ($user) {
                    $query->whereHas('teachers', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
                })
                ->paginate(10);
        }

        return view('admin.users.index', compact('users'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     * Solo accesible por admin.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     * Solo accesible por admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,name'],
            'profile' => ['nullable', 'array'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'profile' => $validated['profile'] ?? null,
        ]);

        $user->syncRoles($validated['roles']);

        if ($request->hasFile('avatar')) {
            $user->medias()->create([
                'file' => $request->file('avatar')->store('avatars', 'public'),
                'type' => 'picture'
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Muestra el perfil de un usuario específico.
     * Admin puede ver cualquier perfil.
     * Teachers solo pueden ver perfiles de sus estudiantes.
     * Users pueden ver su propio perfil.
     */
    public function show(User $user)
    {
        $authUser = auth()->user();
        
        if ($authUser->id !== $user->id && 
            !$authUser->hasRole('admin') && 
            !($authUser->hasRole('teacher') && $this->isTeacherOfStudent($authUser, $user))) {
            abort(403, 'No tienes permiso para ver este perfil.');
        }

        $user->load(['roles', 'medias']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Muestra el formulario para editar un usuario.
     * Solo accesible por admin.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Actualiza un usuario específico en la base de datos.
     * Solo accesible por admin.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,name'],
            'profile' => ['nullable', 'array'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'profile' => $validated['profile'] ?? $user->profile,
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles($validated['roles']);

        if ($request->hasFile('avatar')) {
            // Eliminar avatar anterior si existe
            $user->medias()->where('type', 'picture')->delete();
            
            $user->medias()->create([
                'file' => $request->file('avatar')->store('avatars', 'public'),
                'type' => 'picture'
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Determina la página correcta para redireccionar después de una acción
     */
    private function getRedirectPage()
    {
        $query = User::query();
        $search = request('search');
        $query = $this->applyUserFilters($query, $search);
        
        $totalUsers = $query->count();
        $perPage = config('app.pagination.per_page', 10);
        $maxPage = ceil($totalUsers / $perPage);
        
        if ($this->currentPage > $maxPage && $maxPage > 0) {
            return ['page' => $maxPage];
        }
        
        return ['page' => $this->currentPage];
    }

    /**
     * Elimina temporalmente un usuario (soft delete).
     */
    public function destroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        if (!auth()->user()->hasRole('admin')) {
            return back()->with('error', 'No tienes permisos para eliminar usuarios.');
        }

        $this->currentPage = (int)$request->input('page', 1);
        
        $user->delete();

        $redirectParams = $this->getRedirectPage();
        if ($search = $request->input('search')) {
            $redirectParams['search'] = $search;
        }

        return redirect()->route('admin.users.index', $redirectParams)
            ->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Elimina permanentemente un usuario.
     */
    public function forceDelete(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar permanentemente tu propia cuenta.');
        }

        if ($this->userDeletionService->forceDeleteUser($user)) {
            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario eliminado permanentemente.');
        }

        return back()->with('error', 'Error al eliminar permanentemente el usuario. Por favor, inténtalo de nuevo.');
    }

    /**
     * Restaura un usuario previamente eliminado.
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($this->userDeletionService->restoreUser($user)) {
            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario restaurado correctamente.');
        }

        return back()->with('error', 'Error al restaurar el usuario. Por favor, inténtalo de nuevo.');
    }

    /**
     * Muestra usuarios eliminados.
     */
    public function trashed()
    {
        $users = User::onlyTrashed()->with('roles')->paginate(10);
        return view('admin.users.trashed', compact('users'));
    }

    /**
     * Verifica si un profesor da clase a un estudiante.
     */
    private function isTeacherOfStudent(User $teacher, User $student): bool
    {
        return $student->enrolledCourses()
            ->whereHas('teachers', function ($query) use ($teacher) {
                $query->where('users.id', $teacher->id);
            })
            ->exists();
    }
}
