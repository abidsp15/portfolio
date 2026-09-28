<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Achievement;
use App\Models\Skill;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\EducationImage;
use App\Models\ExperienceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Display admin panel dashboard with all portfolio data for the logged-in user.
     */
    public function index()
    {
        $userId = auth()->id();
        
        // Ensure a profile exists for this user (fallback)
        $profile = Profile::where('user_id', $userId)->first();
        if (!$profile) {
            $profile = Profile::create([
                'user_id' => $userId,
                'name' => auth()->user()->name,
                'headline' => 'Portfolio Owner',
                'phone' => '-',
                'email' => auth()->user()->email,
                'about_text' => 'This is my personal portfolio. Edit this section to write about yourself.',
            ]);
        }

        // Education Sorting
        $eduSort = session('portfolio_sort_education', 'sort_order');
        $eduQuery = Education::where('user_id', $userId)->with('images');
        if ($eduSort === 'institution') {
            $eduQuery->orderBy('institution')->orderBy('sort_order');
        } elseif ($eduSort === 'degree') {
            $eduQuery->orderBy('degree')->orderBy('sort_order');
        } else {
            $eduQuery->orderBy('sort_order')->orderBy('id');
        }
        $education = $eduQuery->get();

        // Experience Sorting
        $expSort = session('portfolio_sort_experience', 'sort_order');
        $expQuery = Experience::where('user_id', $userId)->with('images');
        if ($expSort === 'company') {
            $expQuery->orderBy('company')->orderBy('sort_order');
        } elseif ($expSort === 'role') {
            $expQuery->orderBy('role')->orderBy('sort_order');
        } else {
            $expQuery->orderBy('sort_order')->orderBy('id');
        }
        $experiences = $expQuery->get();

        // Achievements Sorting
        $achSort = session('portfolio_sort_achievement', 'sort_order');
        $achQuery = Achievement::where('user_id', $userId);
        if ($achSort === 'title') {
            $achQuery->orderBy('title')->orderBy('sort_order');
        } elseif ($achSort === 'issuer') {
            $achQuery->orderBy('issuer')->orderBy('sort_order');
        } else {
            $achQuery->orderBy('sort_order')->orderBy('id');
        }
        $achievements = $achQuery->get();

        // Skills Sorting
        $skillSort = session('portfolio_sort_skill', 'sort_order');
        $skillQuery = Skill::where('user_id', $userId);
        if ($skillSort === 'name') {
            $skillQuery->orderBy('name');
        } elseif ($skillSort === 'category') {
            $skillQuery->orderBy('category')->orderBy('sort_order');
        } else {
            $skillQuery->orderBy('sort_order')->orderBy('id');
        }
        $skills = $skillQuery->get();

        // Projects Sorting
        $projSort = session('portfolio_sort_project', 'sort_order');
        $projQuery = Project::where('user_id', $userId)->with('images');
        if ($projSort === 'title') {
            $projQuery->orderBy('title');
        } else {
            $projQuery->orderBy('sort_order')->orderBy('id');
        }
        $projects = $projQuery->get();

        return view('admin.dashboard', compact(
            'profile', 'education', 'experiences', 'achievements', 'skills', 'projects'
        ));
    }

    /**
     * Update the Profile (About & contact info) for the logged-in user.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'headline' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'about_text' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cv' => 'nullable|mimes:pdf|max:10240',
        ]);

        $userId = auth()->id();
        $profile = Profile::where('user_id', $userId)->first() ?? new Profile(['user_id' => $userId]);
        $data = $request->except(['photo', 'cv']);

        if ($request->hasFile('photo')) {
            if ($profile->photo_path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $profile->photo_path));
            }
            $path = $request->file('photo')->store('profile', 'public');
            $data['photo_path'] = '/storage/' . $path;
        }

        if ($request->hasFile('cv')) {
            if ($profile->cv_path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $profile->cv_path));
            }
            $path = $request->file('cv')->store('cv', 'public');
            $data['cv_path'] = '/storage/' . $path;
        }

        $profile->fill($data)->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    // --- EDUCATION CRUD ---

    public function storeEducation(Request $request)
    {
        $data = $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'major' => 'nullable|string|max:255',
            'start_date' => 'required|string|max:100',
            'end_date' => 'required|string|max:100',
            'gpa' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $eduData = collect($data)->except(['images'])->toArray();
        $eduData['user_id'] = auth()->id();
        if (!isset($eduData['sort_order']) || $eduData['sort_order'] === null) {
            $maxSort = Education::where('user_id', auth()->id())->max('sort_order');
            $eduData['sort_order'] = $maxSort !== null ? $maxSort + 1 : 1;
        }

        $education = Education::create($eduData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('education', 'public');
                $education->images()->create([
                    'image_path' => '/storage/' . $path,
                ]);
            }
        }

        return back()->with('success', 'Education item added.');
    }

    public function updateEducation(Request $request, Education $education)
    {
        abort_if($education->user_id !== auth()->id(), 403, 'Unauthorized action.');

        $data = $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'major' => 'nullable|string|max:255',
            'start_date' => 'required|string|max:100',
            'end_date' => 'required|string|max:100',
            'gpa' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $eduData = collect($data)->except(['images'])->toArray();
        if (!isset($eduData['sort_order']) || $eduData['sort_order'] === null) {
            $maxSort = Education::where('user_id', auth()->id())->max('sort_order');
            $eduData['sort_order'] = $maxSort !== null ? $maxSort + 1 : 1;
        }

        $education->update($eduData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('education', 'public');
                $education->images()->create([
                    'image_path' => '/storage/' . $path,
                ]);
            }
        }

        return back()->with('success', 'Education item updated.');
    }

    public function destroyEducation(Education $education)
    {
        abort_if($education->user_id !== auth()->id(), 403, 'Unauthorized action.');

        foreach ($education->images as $image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $image->image_path));
        }

        $education->delete();
        return back()->with('success', 'Education item deleted.');
    }

    // --- EXPERIENCE CRUD ---

    public function storeExperience(Request $request)
    {
        $data = $request->validate([
            'company' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'start_date' => 'required|string|max:100',
            'end_date' => 'required|string|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $expData = collect($data)->except(['images'])->toArray();
        $expData['user_id'] = auth()->id();
        if (!isset($expData['sort_order']) || $expData['sort_order'] === null) {
            $maxSort = Experience::where('user_id', auth()->id())->max('sort_order');
            $expData['sort_order'] = $maxSort !== null ? $maxSort + 1 : 1;
        }

        $experience = Experience::create($expData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('experience', 'public');
                $experience->images()->create([
                    'image_path' => '/storage/' . $path,
                ]);
            }
        }

        return back()->with('success', 'Experience item added.');
    }

    public function updateExperience(Request $request, Experience $experience)
    {
        abort_if($experience->user_id !== auth()->id(), 403, 'Unauthorized action.');

        $data = $request->validate([
            'company' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'start_date' => 'required|string|max:100',
            'end_date' => 'required|string|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $expData = collect($data)->except(['images'])->toArray();
        if (!isset($expData['sort_order']) || $expData['sort_order'] === null) {
            $maxSort = Experience::where('user_id', auth()->id())->max('sort_order');
            $expData['sort_order'] = $maxSort !== null ? $maxSort + 1 : 1;
        }

        $experience->update($expData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('experience', 'public');
                $experience->images()->create([
                    'image_path' => '/storage/' . $path,
                ]);
            }
        }

        return back()->with('success', 'Experience item updated.');
    }

    public function destroyExperience(Experience $experience)
    {
        abort_if($experience->user_id !== auth()->id(), 403, 'Unauthorized action.');

        foreach ($experience->images as $image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $image->image_path));
        }

        $experience->delete();
        return back()->with('success', 'Experience item deleted.');
    }

    // --- ACHIEVEMENT CRUD ---

    public function storeAchievement(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'issuer' => 'nullable|string|max:255',
            'date' => 'nullable|string|max:100',
            'credential_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $data['user_id'] = auth()->id();
        if (!isset($data['sort_order']) || $data['sort_order'] === null) {
            $maxSort = Achievement::where('user_id', auth()->id())->max('sort_order');
            $data['sort_order'] = $maxSort !== null ? $maxSort + 1 : 1;
        }

        Achievement::create($data);
        return back()->with('success', 'Achievement item added.');
    }

    public function updateAchievement(Request $request, Achievement $achievement)
    {
        abort_if($achievement->user_id !== auth()->id(), 403, 'Unauthorized action.');

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'issuer' => 'nullable|string|max:255',
            'date' => 'nullable|string|max:100',
            'credential_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        if (!isset($data['sort_order']) || $data['sort_order'] === null) {
            $maxSort = Achievement::where('user_id', auth()->id())->max('sort_order');
            $data['sort_order'] = $maxSort !== null ? $maxSort + 1 : 1;
        }

        $achievement->update($data);
        return back()->with('success', 'Achievement item updated.');
    }

    public function destroyAchievement(Achievement $achievement)
    {
        abort_if($achievement->user_id !== auth()->id(), 403, 'Unauthorized action.');
        $achievement->delete();
        return back()->with('success', 'Achievement item deleted.');
    }

    // --- SKILL CRUD ---

    public function storeSkill(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Technical,Soft Skill,Language',
            'sort_order' => 'nullable|integer',
        ]);

        $data['user_id'] = auth()->id();
        if (!isset($data['sort_order']) || $data['sort_order'] === null) {
            $maxSort = Skill::where('user_id', auth()->id())
                            ->where('category', $data['category'])
                            ->max('sort_order');
            $data['sort_order'] = $maxSort !== null ? $maxSort + 1 : 1;
        }

        Skill::create($data);
        return back()->with('success', 'Skill added.');
    }

    public function updateSkill(Request $request, Skill $skill)
    {
        abort_if($skill->user_id !== auth()->id(), 403, 'Unauthorized action.');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Technical,Soft Skill,Language',
            'sort_order' => 'nullable|integer',
        ]);

        $categoryChanged = $data['category'] !== $skill->category;

        if (!isset($data['sort_order']) || $data['sort_order'] === null || ($categoryChanged && intval($data['sort_order']) === intval($skill->sort_order))) {
            $maxSort = Skill::where('user_id', auth()->id())
                            ->where('category', $data['category'])
                            ->max('sort_order');
            $data['sort_order'] = $maxSort !== null ? $maxSort + 1 : 1;
        }

        $skill->update($data);
        return back()->with('success', 'Skill updated.');
    }

    public function destroySkill(Skill $skill)
    {
        abort_if($skill->user_id !== auth()->id(), 403, 'Unauthorized action.');
        $skill->delete();
        return back()->with('success', 'Skill deleted.');
    }

    // --- PROJECT CRUD ---

    public function storeProject(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'video' => 'nullable|mimes:mp4,mov,ogg,qt|max:512000',
            'video_thumbnail' => 'nullable|string',
            'project_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $data = $request->only(['title', 'description', 'project_url', 'sort_order']);
        $data['user_id'] = auth()->id();

        if (!isset($data['sort_order']) || $data['sort_order'] === null) {
            $maxSort = Project::where('user_id', auth()->id())->max('sort_order');
            $data['sort_order'] = $maxSort !== null ? $maxSort + 1 : 1;
        }

        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('projects', 'public');
            $data['video_path'] = '/storage/' . $path;

            // Handle frontend video thumbnail Base64 upload
            if ($request->filled('video_thumbnail')) {
                $base64Str = $request->input('video_thumbnail');
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Str, $type)) {
                    $base64Str = substr($base64Str, strpos($base64Str, ',') + 1);
                    $type = strtolower($type[1]);
                    $image = base64_decode($base64Str);
                    if ($image !== false) {
                        $filename = 'thumb_' . time() . '_' . uniqid() . '.' . $type;
                        $thumbPath = 'projects/thumbnails/' . $filename;
                        Storage::disk('public')->put($thumbPath, $image);
                        $data['video_thumbnail_path'] = '/storage/' . $thumbPath;
                    }
                }
            }
        }

        $project = Project::create($data);

        // Upload multiple images
        if ($request->hasFile('images')) {
            $firstImagePath = null;
            foreach ($request->file('images') as $index => $imageFile) {
                $path = $imageFile->store('projects', 'public');
                $imagePath = '/storage/' . $path;
                
                $project->images()->create([
                    'image_path' => $imagePath,
                ]);

                if ($index === 0) {
                    $firstImagePath = $imagePath;
                }
            }

            // Sync backward compatibility column
            if ($firstImagePath) {
                $project->update(['image_path' => $firstImagePath]);
            }
        }

        return back()->with('success', 'Project added.');
    }

    public function updateProject(Request $request, Project $project)
    {
        abort_if($project->user_id !== auth()->id(), 403, 'Unauthorized action.');

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'video' => 'nullable|mimes:mp4,mov,ogg,qt|max:512000',
            'video_thumbnail' => 'nullable|string',
            'project_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $data = $request->only(['title', 'description', 'project_url', 'sort_order']);

        if (!isset($data['sort_order']) || $data['sort_order'] === null) {
            $maxSort = Project::where('user_id', auth()->id())->max('sort_order');
            $data['sort_order'] = $maxSort !== null ? $maxSort + 1 : 1;
        }

        if ($request->hasFile('video')) {
            if ($project->video_path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $project->video_path));
            }
            $path = $request->file('video')->store('projects', 'public');
            $data['video_path'] = '/storage/' . $path;

            // Handle frontend video thumbnail Base64 upload (only when video is updated)
            if ($request->filled('video_thumbnail')) {
                $base64Str = $request->input('video_thumbnail');
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Str, $type)) {
                    $base64Str = substr($base64Str, strpos($base64Str, ',') + 1);
                    $type = strtolower($type[1]);
                    $image = base64_decode($base64Str);
                    if ($image !== false) {
                        // Delete old thumbnail
                        if ($project->video_thumbnail_path) {
                            Storage::disk('public')->delete(str_replace('/storage/', '', $project->video_thumbnail_path));
                        }
                        $filename = 'thumb_' . time() . '_' . uniqid() . '.' . $type;
                        $thumbPath = 'projects/thumbnails/' . $filename;
                        Storage::disk('public')->put($thumbPath, $image);
                        $data['video_thumbnail_path'] = '/storage/' . $thumbPath;
                    }
                }
            }
        }

        $project->update($data);

        // Upload and append additional images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('projects', 'public');
                $imagePath = '/storage/' . $path;
                
                $project->images()->create([
                    'image_path' => $imagePath,
                ]);
            }
        }

        // Sync first image path for backward compatibility
        $firstImage = $project->images()->first();
        $project->update(['image_path' => $firstImage ? $firstImage->image_path : null]);

        return back()->with('success', 'Project updated.');
    }

    public function destroyProject(Project $project)
    {
        abort_if($project->user_id !== auth()->id(), 403, 'Unauthorized action.');

        // Delete all associated image files
        foreach ($project->images as $image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $image->image_path));
        }

        if ($project->video_path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $project->video_path));
        }

        if ($project->video_thumbnail_path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $project->video_thumbnail_path));
        }

        $project->delete();
        return back()->with('success', 'Project deleted.');
    }

    /**
     * Update sorting preferences in Session.
     */
    public function updateSorting(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:education,experience,achievement,skill,project',
            'sort_by' => 'required|string|max:50',
        ]);

        session(['portfolio_sort_' . $request->type => $request->sort_by]);

        return back()->with('success', ucfirst($request->type) . ' sorting preference updated.');
    }

    /**
     * Bulk Delete Education items.
     */
    public function bulkDestroyEducation(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->withErrors(['No items selected.']);
        }

        $items = Education::where('user_id', auth()->id())->whereIn('id', $ids)->with('images')->get();
        foreach ($items as $item) {
            foreach ($item->images as $image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $image->image_path));
            }
            $item->delete();
        }
        return back()->with('success', 'Selected education records deleted successfully.');
    }

    /**
     * Bulk Delete Experience items.
     */
    public function bulkDestroyExperience(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->withErrors(['No items selected.']);
        }

        $items = Experience::where('user_id', auth()->id())->whereIn('id', $ids)->with('images')->get();
        foreach ($items as $item) {
            foreach ($item->images as $image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $image->image_path));
            }
            $item->delete();
        }
        return back()->with('success', 'Selected experience records deleted successfully.');
    }

    /**
     * Bulk Delete Achievement items.
     */
    public function bulkDestroyAchievement(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->withErrors(['No items selected.']);
        }

        Achievement::where('user_id', auth()->id())->whereIn('id', $ids)->delete();
        return back()->with('success', 'Selected achievement records deleted successfully.');
    }

    /**
     * Bulk Delete Skill items.
     */
    public function bulkDestroySkill(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->withErrors(['No items selected.']);
        }

        Skill::where('user_id', auth()->id())->whereIn('id', $ids)->delete();
        return back()->with('success', 'Selected skills deleted successfully.');
    }

    /**
     * Bulk Delete Project items.
     */
    public function bulkDestroyProject(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->withErrors(['No items selected.']);
        }

        $projects = Project::where('user_id', auth()->id())->whereIn('id', $ids)->with('images')->get();
        foreach ($projects as $project) {
            foreach ($project->images as $image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $image->image_path));
            }
            if ($project->video_path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $project->video_path));
            }
            if ($project->video_thumbnail_path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $project->video_thumbnail_path));
            }
            $project->delete();
        }

        return back()->with('success', 'Selected projects deleted successfully.');
    }

    /**
     * AJAX endpoint to delete a specific project image.
     */
    public function destroyProjectImage(ProjectImage $projectImage)
    {
        $project = $projectImage->project;
        
        // Authorize action (ensure the user owns the parent project)
        abort_if($project->user_id !== auth()->id(), 403, 'Unauthorized action.');

        // Delete from storage
        Storage::disk('public')->delete(str_replace('/storage/', '', $projectImage->image_path));
        
        // Delete from database
        $projectImage->delete();

        // Update fallback image_path for backward compatibility
        $firstImage = $project->images()->first();
        $project->update(['image_path' => $firstImage ? $firstImage->image_path : null]);

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.',
            'fallback_path' => $firstImage ? $firstImage->image_path : null,
        ]);
    }

    /**
     * AJAX endpoint to delete a specific education image.
     */
    public function destroyEducationImage(EducationImage $educationImage)
    {
        $education = $educationImage->education;
        
        // Authorize action
        abort_if($education->user_id !== auth()->id(), 403, 'Unauthorized action.');

        // Delete from storage
        Storage::disk('public')->delete(str_replace('/storage/', '', $educationImage->image_path));
        
        // Delete database record
        $educationImage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.',
        ]);
    }

    /**
     * AJAX endpoint to delete a specific experience image.
     */
    public function destroyExperienceImage(ExperienceImage $experienceImage)
    {
        $experience = $experienceImage->experience;
        
        // Authorize action
        abort_if($experience->user_id !== auth()->id(), 403, 'Unauthorized action.');

        // Delete from storage
        Storage::disk('public')->delete(str_replace('/storage/', '', $experienceImage->image_path));
        
        // Delete database record
        $experienceImage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.',
        ]);
    }
}
