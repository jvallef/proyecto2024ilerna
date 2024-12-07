<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Path;
use App\Services\CourseService;
use App\Http\Requests\CourseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class CourseController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
        
        // Solo admin puede gestionar courses
        $this->middleware(['auth', 'role:admin'])->only([
            'privateIndex', 'privateShow', 'privateCreate', 'privateStore', 
            'privateEdit', 'privateUpdate', 'privateDestroy',
            'privateTrashed', 'privateRestore', 'privateForceDelete'
        ]);
        
        // Acceso a sección educativa requiere autenticación
        $this->middleware(['auth'])->only([
            'educaIndex', 'educaShow', 'educaProgress',
            'enroll', 'unenroll', 'enrollStudent', 'unenrollStudent'
        ]);
    }

    /**
     * Display a public listing of published courses.
     */
    public function publicIndex(Request $request)
    {
        try {
            $perPage = env('PAGINATION_PER_PAGE', 12);
            $query = Course::with(['author', 'paths', 'contents', 'enrolledUsers'])->where('status', 'published');

            // Aplicar búsqueda si existe
            $query = $this->applyCourseFilters($query, $request->input('search'));

            // Obtener cursos destacados y regulares
            $featuredQuery = clone $query;
            $regularQuery = clone $query;

            $featuredCourses = $featuredQuery->where('featured', true)->get();
            $regularCourses = $regularQuery->paginate($perPage);

            // Mantener los parámetros en la paginación
            if ($request->has('search')) {
                $regularCourses->appends(['search' => $request->input('search')]);
            }

            return view('courses.index', compact('featuredCourses', 'regularCourses'));
            
        } catch (\Exception $e) {
            Log::error('Error loading courses index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar los cursos. Por favor, actualiza la página.');
        }
    }

    /**
     * Display a private listing of all courses for admin.
     */
    public function privateIndex(Request $request)
    {
        try {
            $perPage = env('PAGINATION_PER_PAGE', 12);
            $query = Course::with(['author', 'paths']);

            // Aplicar búsqueda si existe
            $query = $this->applyCourseFilters($query, $request->input('search'));

            // Obtener cursos destacados y regulares
            $featuredQuery = clone $query;
            $regularQuery = clone $query;

            $featuredCourses = $featuredQuery->where('featured', true)->get();
            $regularCourses = $regularQuery->paginate($perPage);

            // Mantener los parámetros en la paginación
            if ($request->has('search')) {
                $regularCourses->appends(['search' => $request->input('search')]);
            }

            return view('admin.courses.index', compact('featuredCourses', 'regularCourses'));
            
        } catch (\Exception $e) {
            Log::error('Error loading admin courses index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el listado de cursos.');
        }
    }

    /**
     * Display the educational listing of courses.
     */
    public function educaIndex(Request $request)
    {
        try {
            $perPage = env('PAGINATION_PER_PAGE', 12);
            $query = Course::with(['author', 'paths'])->where('status', 'published');

            // Aplicar búsqueda si existe
            $query = $this->applyCourseFilters($query, $request->input('search'));

            $courses = $query->paginate($perPage);

            // Mantener los parámetros en la paginación
            if ($request->has('search')) {
                $courses->appends(['search' => $request->input('search')]);
            }

            return view('educa.courses.index', compact('courses'));
            
        } catch (\Exception $e) {
            Log::error('Error loading educational courses index: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el listado de cursos.');
        }
    }

    /**
     * Show the form for creating a new course.
     */
    public function privateCreate()
    {
        $course = new Course();
        $pathsList = Path::all()->pluck('name', 'id');
        return view('admin.courses.create', compact('course', 'pathsList'));
    }

    /**
     * Store a newly created course in storage.
     */
    public function privateStore(CourseRequest $request)
    {
        try {
            \DB::beginTransaction();

            $data = $request->validated();
            $data['author_id'] = auth()->id();
            
            $course = $this->courseService->create($data);

            if ($request->hasFile('cover')) {
                $course->addMediaFromRequest('cover')
                    ->toMediaCollection('cover');
            }

            \DB::commit();
            return redirect()->route('admin.courses.index')
                           ->with('success', 'Curso creado correctamente.')
                           ->withHeaders([
                               'Cache-Control' => 'no-cache, no-store, must-revalidate',
                               'Pragma' => 'no-cache',
                               'Expires' => '0'
                           ]);
                           
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error creating course: ' . $e->getMessage());
            return back()->with('error', 'No se pudo crear el curso.')
                        ->withInput();
        }
    }

    /**
     * Display the specified course for public view.
     */
    public function publicShow($slug)
    {
        try {
            $course = Course::where('slug', $slug)
                          ->where('status', 'published')
                          ->with(['author', 'paths', 'contents', 'enrolledUsers'])
                          ->firstOrFail();
            return view('courses.show', compact('course'));
            
        } catch (\Exception $e) {
            Log::error('Error showing public course: ' . $e->getMessage());
            abort(404, 'Curso solicitado no encontrado');
        }
    }

    /**
     * Display the specified course for admin.
     */
    public function privateShow(Course $course)
    {
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Display the specified course for educational view.
     */
    public function educaShow($slug)
    {
        try {
            $course = Course::where('slug', $slug)
                          ->where('status', 'published')
                          ->with(['author', 'paths', 'contents', 'enrolledUsers'])
                          ->firstOrFail();
                          
            $userEnrollment = null;
            if (auth()->check()) {
                $userEnrollment = $course->enrolledUsers()
                                       ->where('user_id', auth()->id())
                                       ->first();
            }
            
            return view('educa.courses.show', compact('course', 'userEnrollment'));
            
        } catch (\Exception $e) {
            Log::error('Error showing educational course: ' . $e->getMessage());
            abort(404, 'Curso solicitado no encontrado');
        }
    }

    /**
     * Show the form for editing the specified course.
     */
    public function privateEdit(Course $course)
    {
        $pathsList = Path::all()->pluck('name', 'id');
        return view('admin.courses.edit', compact('course', 'pathsList'));
    }

    /**
     * Update the specified course in storage.
     */
    public function privateUpdate(CourseRequest $request, Course $course)
    {
        try {
            \DB::beginTransaction();
            
            $data = $request->validated();
            $this->courseService->update($course, $data);

            if ($request->hasFile('cover')) {
                $course->clearMediaCollection('cover');
                $course->addMediaFromRequest('cover')
                    ->toMediaCollection('cover');
            }

            \DB::commit();
            return redirect()->route('admin.courses.index')
                           ->with('success', 'Curso actualizado correctamente.');
                           
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error updating course: ' . $e->getMessage());
            return back()->with('error', 'No se pudo actualizar el curso.')
                        ->withInput();
        }
    }

    /**
     * Remove the specified course from storage.
     */
    public function privateDestroy(Course $course)
    {
        try {
            $this->courseService->delete($course);
            return redirect()->route('admin.courses.index')
                           ->with('success', 'Curso eliminado correctamente.');
                           
        } catch (\Exception $e) {
            Log::error('Error deleting course: ' . $e->getMessage());
            return back()->with('error', 'No se pudo eliminar el curso.');
        }
    }

    /**
     * Display a listing of trashed courses.
     */
    public function privateTrashed(Request $request)
    {
        try {
            $perPage = env('PAGINATION_PER_PAGE', 12);
            $query = Course::onlyTrashed()->with(['author', 'paths']);

            // Aplicar búsqueda si existe
            $query = $this->applyCourseFilters($query, $request->input('search'));

            $courses = $query->paginate($perPage);

            // Mantener los parámetros en la paginación
            if ($request->has('search')) {
                $courses->appends(['search' => $request->input('search')]);
            }

            return view('admin.courses.trashed', compact('courses'));
            
        } catch (\Exception $e) {
            Log::error('Error loading trashed courses: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar los cursos eliminados.');
        }
    }

    /**
     * Restore the specified course from trash.
     */
    public function privateRestore($id)
    {
        try {
            $course = Course::onlyTrashed()->findOrFail($id);
            $course->restore();
            
            return redirect()->route('admin.courses.trashed')
                           ->with('success', 'Curso restaurado correctamente.');
                           
        } catch (\Exception $e) {
            Log::error('Error restoring course: ' . $e->getMessage());
            return back()->with('error', 'Error al restaurar el curso.');
        }
    }

    /**
     * Force delete the specified course from storage.
     */
    public function privateForceDelete($id)
    {
        try {
            $course = Course::onlyTrashed()->findOrFail($id);
            $course->forceDelete();
            
            return redirect()->route('admin.courses.trashed')
                           ->with('success', 'Curso eliminado permanentemente.');
                           
        } catch (\Exception $e) {
            Log::error('Error force deleting course: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar permanentemente el curso.');
        }
    }

    /**
     * Display the educational progress for a course.
     */
    public function educaProgress($slug)
    {
        try {
            $course = Course::where('slug', $slug)
                          ->where('status', 'published')
                          ->with(['author', 'paths', 'contents', 'enrolledUsers'])
                          ->firstOrFail();
                          
            $userEnrollment = $course->enrolledUsers()
                                   ->where('user_id', auth()->id())
                                   ->firstOrFail();
            
            return view('educa.courses.progress', compact('course', 'userEnrollment'));
            
        } catch (\Exception $e) {
            Log::error('Error showing course progress: ' . $e->getMessage());
            abort(404, 'Progreso del curso solicitado no encontrado');
        }
    }

    /**
     * Enroll current user in a course (student).
     */
    public function enroll(Course $course)
    {
        try {
            $this->courseService->enrollUser($course, auth()->user());
            return back()->with('success', 'Te has matriculado correctamente en el curso.');
            
        } catch (\Exception $e) {
            Log::error('Error enrolling in course: ' . $e->getMessage());
            return back()->with('error', 'Error al matricularte en el curso.');
        }
    }

    /**
     * Unenroll current user from a course (student).
     */
    public function unenroll(Course $course)
    {
        try {
            $this->courseService->unenrollUser($course, auth()->user());
            return back()->with('success', 'Te has desmatriculado correctamente del curso.');
            
        } catch (\Exception $e) {
            Log::error('Error unenrolling from course: ' . $e->getMessage());
            return back()->with('error', 'Error al desmatricularte del curso.');
        }
    }

    /**
     * Enroll a student in a course (teacher).
     */
    public function enrollStudent(Course $course, Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);
            
            $this->courseService->enrollUser($course, $request->user_id, 'student');
            return back()->with('success', 'Estudiante matriculado correctamente.');
            
        } catch (\Exception $e) {
            Log::error('Error enrolling student: ' . $e->getMessage());
            return back()->with('error', 'Error al matricular al estudiante.');
        }
    }

    /**
     * Unenroll a student from a course (teacher).
     */
    public function unenrollStudent(Course $course, Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);
            
            $this->courseService->unenrollUser($course, $request->user_id);
            return back()->with('success', 'Estudiante desmatriculado correctamente.');
            
        } catch (\Exception $e) {
            Log::error('Error unenrolling student: ' . $e->getMessage());
            return back()->with('error', 'Error al desmatricular al estudiante.');
        }
    }

    /**
     * Apply search filters to the course query.
     */
    protected function applyCourseFilters($query, $search = null)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        return $query;
    }
}
