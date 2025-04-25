<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\ReportTemplate;
use App\Services\ReportDataService;
use App\Http\Controllers\Controller;
use Carbon\Carbon; // <-- Import Carbon
use Illuminate\Http\JsonResponse; // <-- Import JsonResponse

class ReportGeneratorController extends Controller
{
    public function index(): View
    {
        // Fetch all templates, maybe just id and name for the dropdown
        // Eager load content if needed immediately, or fetch via JS later
        // Let's fetch all relevant data for Alpine initially
        $templates = ReportTemplate::select('id', 'name')->get();

        // Return the generator view, passing the templates
        return view('generator.index', compact('templates'));
    }
     /**
     * Generate the report based on template and context.
     */
    public function generate(Request $request): JsonResponse // <-- Return JSON
    {
        // --- Validation ---
        $validated = $request->validate([
            'template_id' => 'required|integer|exists:report_templates,id',
            'report_date' => 'required|date_format:Y-m-d', // Expecting YYYY-MM-DD format
        ]);

        // --- Setup ---
        try {
            Carbon::setLocale('id');
            $template = ReportTemplate::findOrFail($validated['template_id']);
            $reportDate = Carbon::parse($validated['report_date']); // Create Carbon instance
            // Ensure locale is set if using translatedFormat in service
            $reportService = new ReportDataService($reportDate); // Instantiate service with date context

            $templateContent = $template->content;
            $generatedContent = $templateContent;

            // --- Find Placeholders ---
            // Regex to find {{placeholder_name}}
            $regex = '/\{\{([a-zA-Z0-9_]+)\}\}/';
            preg_match_all($regex, $templateContent, $matches);

            $placeholders = $matches[1] ?? []; // Get the names (captured group)

            // --- Fetch Data and Replace Placeholders ---
            $dataValues = [];
            foreach ($placeholders as $placeholder) {
                if (!isset($dataValues[$placeholder])) { // Avoid redundant calls if placeholder repeats
                    // Get data using the service's helper method
                    $value = $reportService->getDataForPlaceholder($placeholder);

                    // Store value (even if null, handle replacement below)
                     $dataValues[$placeholder] = $value;

                    // Perform replacement
                     $placeholderTag = '{{' . $placeholder . '}}';
                     // Replace with fetched value, or keep the tag if data is null (or show N/A?)
                     $replacement = $value ?? 'N/A'; // Or $value ?? $placeholderTag;
                     $generatedContent = str_replace($placeholderTag, $replacement, $generatedContent);
                }
            }


            // --- Return Response ---
            return response()->json([
                'success' => true,
                'generated_report' => $generatedContent,
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            // Log::error('Report generation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan. Silakan coba lagi. Error: ' . $e->getMessage() // More detailed error for debugging if needed
            ], 500); // Internal Server Error
        }
    }
}
