<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonLearnedRequest;
use App\Http\Requests\UpdateLessonLearnedRequest;
use App\Models\LessonLearned;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LessonLearnedController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            LessonLearned::query()->with('project')->latest('recorded_on'),
            fn (LessonLearned $lesson) => $lesson->toInertia()
        );

        return Inertia::render('Reports/LessonsLearned', [
            'lessons' => $page['data'],
            'pagination' => $page['pagination'],
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Reports/LessonsLearned', [
            'lessons' => [],
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreLessonLearnedRequest $request): RedirectResponse
    {
        LessonLearned::query()->create($request->safe()->only([
            'project_id',
            'title',
            'category',
            'description',
            'impact',
            'recorded_on',
        ]));

        return redirect()->route('reports.lessons-learned')->with('success', 'Lesson recorded.');
    }

    public function show(LessonLearned $lessonLearned): Response
    {
        $lessonLearned->load('project');

        return Inertia::render('Reports/LessonsLearned', [
            'lessons' => [$lessonLearned->toInertia()],
            'lesson' => $lessonLearned->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(LessonLearned $lessonLearned): Response
    {
        $lessonLearned->load('project');

        return Inertia::render('Reports/LessonsLearned', [
            'lessons' => [$lessonLearned->toInertia()],
            'lesson' => $lessonLearned->toInertia(),
            'projects' => $this->projectOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateLessonLearnedRequest $request, LessonLearned $lessonLearned): RedirectResponse
    {
        $lessonLearned->update($request->safe()->only([
            'project_id',
            'title',
            'category',
            'description',
            'impact',
            'recorded_on',
        ]));

        return redirect()->route('reports.lessons-learned')->with('success', 'Lesson updated.');
    }

    public function destroy(LessonLearned $lessonLearned): RedirectResponse
    {
        $lessonLearned->delete();

        return redirect()->route('reports.lessons-learned')->with('success', 'Lesson removed.');
    }
}
