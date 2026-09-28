<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Achievement;
use App\Models\Skill;
use App\Models\Project;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PortfolioController extends Controller
{
    /**
     * Display the public portfolio page (redirects to login/dashboard in routes,
     * but left here as a fallback mapped to the current authenticated user).
     */
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        return $this->preview();
    }

    /**
     * Display the portfolio page in preview mode for the logged-in user.
     */
    public function preview()
    {
        $userId = auth()->id();
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

        $data = $this->getSortedData($userId);
        $education = $data['education'];
        $experiences = $data['experiences'];
        $achievements = $data['achievements'];
        $skills = $data['skills'];
        $projects = $data['projects'];
        $isPreview = true;

        return view('portfolio', compact(
            'profile', 'education', 'experiences', 'achievements', 'skills', 'projects', 'isPreview'
        ));
    }

    /**
     * Export the authenticated user's portfolio data as a clean PDF resume.
     */
    public function exportPdf()
    {
        $userId = auth()->id();
        $profile = Profile::where('user_id', $userId)->first();
        if (!$profile) {
            abort(404, 'Profile data not found');
        }

        $data = $this->getSortedData($userId);
        $education = $data['education'];
        $experiences = $data['experiences'];
        $achievements = $data['achievements'];
        $skills = $data['skills'];
        $projects = $data['projects'];

        // Configure Dompdf options for performance and CSS compatibility
        $pdf = Pdf::loadView('portfolio-pdf', compact('profile', 'education', 'experiences', 'achievements', 'skills', 'projects'))
                  ->setPaper('a4', 'portrait')
                  ->setWarnings(false);

        $filename = 'Resume_' . str_replace(' ', '_', $profile->name) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Helper to get database lists sorted according to session preferences.
     */
    private function getSortedData($userId)
    {
        // Education
        $eduSort = session('portfolio_sort_education', 'sort_order');
        $eduQuery = Education::where('user_id', $userId)->with('images');
        if ($eduSort === 'institution') {
            $eduQuery->orderBy('institution')->orderBy('sort_order');
        } elseif ($eduSort === 'degree') {
            $eduQuery->orderBy('degree')->orderBy('sort_order');
        } else {
            $eduQuery->orderBy('sort_order')->orderBy('id');
        }

        // Experience
        $expSort = session('portfolio_sort_experience', 'sort_order');
        $expQuery = Experience::where('user_id', $userId)->with('images');
        if ($expSort === 'company') {
            $expQuery->orderBy('company')->orderBy('sort_order');
        } elseif ($expSort === 'role') {
            $expQuery->orderBy('role')->orderBy('sort_order');
        } else {
            $expQuery->orderBy('sort_order')->orderBy('id');
        }

        // Achievements
        $achSort = session('portfolio_sort_achievement', 'sort_order');
        $achQuery = Achievement::where('user_id', $userId);
        if ($achSort === 'title') {
            $achQuery->orderBy('title')->orderBy('sort_order');
        } elseif ($achSort === 'issuer') {
            $achQuery->orderBy('issuer')->orderBy('sort_order');
        } else {
            $achQuery->orderBy('sort_order')->orderBy('id');
        }

        // Skills
        $skillSort = session('portfolio_sort_skill', 'sort_order');
        $skillQuery = Skill::where('user_id', $userId);
        if ($skillSort === 'name') {
            $skillQuery->orderBy('name');
        } elseif ($skillSort === 'category') {
            $skillQuery->orderBy('category')->orderBy('sort_order');
        } else {
            $skillQuery->orderBy('sort_order')->orderBy('id');
        }

        // Projects
        $projSort = session('portfolio_sort_project', 'sort_order');
        $projQuery = Project::where('user_id', $userId)->with('images');
        if ($projSort === 'title') {
            $projQuery->orderBy('title');
        } else {
            $projQuery->orderBy('sort_order')->orderBy('id');
        }

        return [
            'education' => $eduQuery->get(),
            'experiences' => $expQuery->get(),
            'achievements' => $achQuery->get(),
            'skills' => $skillQuery->get(),
            'projects' => $projQuery->get(),
        ];
    }
}
