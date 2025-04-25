<?php

namespace App\Http\Controllers;

use App\Models\ReportTemplate;
use Illuminate\Http\Request;

class ReportTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        $templates = ReportTemplate::all();

        return view("report_templates.index", compact("templates"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view("report_templates.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name' => 'required',
            'content'=> 'required',
        ]);

        ReportTemplate::create($request->all());

        return redirect()->route("template.index")->with('success', 'Template berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReportTemplate $reportTemplate)
    {
        //
        return redirect()->route("template.index",$reportTemplate);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReportTemplate $template)
    {
        //
        return view("report_templates.edit", compact("template") );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReportTemplate $template)
    {
        //
        $validated = $request->validate([
            'name' => 'required',
            'content'=> 'required',
        ]);

        $template->update($validated);

        return redirect()->route("template.index")->with('success', 'Template berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReportTemplate $reportTemplate)
    {
        //
        $reportTemplate->delete();

        return redirect()->route("template.index")->with('success', 'Template berhasil dihapus');
    }

    public function showGenerator(){
        $templates = ReportTemplate::orderBy("name")->get();
        return view("report_templates.generator", compact("templates"));
    }
}
