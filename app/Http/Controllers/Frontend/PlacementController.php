<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Category;
use App\Models\PlacementGuide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles public Placement Preparation hub, guide details, and resource downloads.
 */
class PlacementController
{
    /**
     * Display the dynamic placement preparation hub.
     */
    public function index(Request $request): View
    {
        $selectedCategory = $request->query('category');

        $guidesQuery = PlacementGuide::published()->ordered();

        if ($selectedCategory && array_key_exists($selectedCategory, PlacementGuide::getCategories())) {
            $guidesQuery->category($selectedCategory);
        }

        $guides = $guidesQuery->get();

        // Specific spotlight guides
        $roadmapGuide = PlacementGuide::published()->where('category', 'roadmap')->ordered()->first();
        $resumeGuide = PlacementGuide::published()->where('category', 'resume')->ordered()->first();
        $interviewGuide = PlacementGuide::published()->where('category', 'interview')->ordered()->first();
        $technicalGuide = PlacementGuide::published()->where('category', 'technical')->ordered()->first();

        // Relevant MCQ categories for direct integration
        $mcqCategories = Category::whereIn('slug', [
            'arithmetic-aptitude',
            'data-interpretation',
            'logical-reasoning',
            'verbal-ability',
            'programming',
            'engineering',
        ])->orderBy('display_order')->get();

        $categories = PlacementGuide::getCategories();

        return view('frontend.placement.index', [
            'guides' => $guides,
            'selectedCategory' => $selectedCategory,
            'categories' => $categories,
            'roadmapGuide' => $roadmapGuide,
            'resumeGuide' => $resumeGuide,
            'interviewGuide' => $interviewGuide,
            'technicalGuide' => $technicalGuide,
            'mcqCategories' => $mcqCategories,
        ]);
    }

    /**
     * Display a specific published placement guide.
     */
    public function show(string $slug): View
    {
        $guide = PlacementGuide::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedGuides = PlacementGuide::published()
            ->where('id', '!=', $guide->id)
            ->where('category', $guide->category)
            ->ordered()
            ->take(3)
            ->get();

        if ($relatedGuides->isEmpty()) {
            $relatedGuides = PlacementGuide::published()
                ->where('id', '!=', $guide->id)
                ->ordered()
                ->take(3)
                ->get();
        }

        // Relevant MCQ categories for practice card
        $mcqCategories = Category::whereIn('slug', [
            'arithmetic-aptitude',
            'data-interpretation',
            'logical-reasoning',
            'verbal-ability',
            'programming',
        ])->orderBy('display_order')->take(4)->get();

        return view('frontend.placement.show', [
            'guide' => $guide,
            'relatedGuides' => $relatedGuides,
            'mcqCategories' => $mcqCategories,
        ]);
    }

    /**
     * Securely download or redirect to the guide's downloadable resource.
     */
    public function download(string $slug): Response|RedirectResponse
    {
        $guide = PlacementGuide::published()
            ->where('slug', $slug)
            ->firstOrFail();

        if (! $guide->hasDownloadableAsset()) {
            return redirect()->route('placement.show', $slug)
                ->with('error', 'The requested resource template is currently being finalized by our editorial team. Please check back shortly.');
        }

        $asset = $guide->downloadable_asset;
        $downloadName = $guide->downloadable_asset_name ?: (basename($asset) ?: 'aptimedha-resource.pdf');

        // External/Cloudinary URL
        if (str_starts_with($asset, 'http://') || str_starts_with($asset, 'https://')) {
            return redirect()->away($asset);
        }

        // Local storage asset
        if (Storage::disk('public')->exists($asset)) {
            return Storage::disk('public')->download($asset, $downloadName);
        }

        // Check if file is in public folder
        if (file_exists(public_path($asset))) {
            return response()->download(public_path($asset), $downloadName);
        }

        return redirect()->route('placement.show', $slug)
            ->with('error', 'The downloadable file could not be located on the server. Our administrators have been notified.');
    }
}
