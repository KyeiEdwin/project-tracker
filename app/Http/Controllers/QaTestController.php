<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQaTestRequest;
use App\Http\Requests\UpdateQaTestRequest;
use App\Models\QaTest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class QaTestController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            QaTest::query()->with('project')->latest(),
            fn (QaTest $test) => $test->toInertia()
        );

        $base = QaTest::query();

        return Inertia::render('Quality/QaTesting', [
            'testCases' => $page['data'],
            'pagination' => $page['pagination'],
            'stats' => [
                'total' => (clone $base)->count(),
                'passed' => (clone $base)->where('status', 'passed')->count(),
                'failed' => (clone $base)->where('status', 'failed')->count(),
                'pending' => (clone $base)->where('status', 'pending')->count(),
            ],
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Quality/QaTesting', [
            'testCases' => [],
            'stats' => ['total' => 0, 'passed' => 0, 'failed' => 0, 'pending' => 0],
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreQaTestRequest $request): RedirectResponse
    {
        $test = QaTest::query()->create($request->safe()->except('steps'));

        foreach ($request->input('steps', []) as $index => $step) {
            $test->steps()->create([
                'instruction' => $step['instruction'],
                'expected_result' => $step['expected_result'] ?? null,
                'status' => 'pending',
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('quality.qa-testing')->with('success', 'Test case created.');
    }

    public function show(QaTest $qaTest): Response
    {
        $qaTest->load(['project', 'steps']);

        return Inertia::render('Quality/QaTesting', [
            'testCases' => [$qaTest->toInertia()],
            'qaTest' => $qaTest->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(QaTest $qaTest): Response
    {
        $qaTest->load(['project', 'steps']);

        return Inertia::render('Quality/QaTesting', [
            'testCases' => [$qaTest->toInertia()],
            'qaTest' => $qaTest->toInertia(),
            'projects' => $this->projectOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateQaTestRequest $request, QaTest $qaTest): RedirectResponse
    {
        $qaTest->update($request->safe()->except('steps'));

        if ($request->has('steps')) {
            $qaTest->steps()->delete();
            foreach ($request->input('steps', []) as $index => $step) {
                $qaTest->steps()->create([
                    'instruction' => $step['instruction'],
                    'expected_result' => $step['expected_result'] ?? null,
                    'actual_result' => $step['actual_result'] ?? null,
                    'status' => $step['status'] ?? 'pending',
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('quality.qa-testing')->with('success', 'Test case updated.');
    }

    public function destroy(QaTest $qaTest): RedirectResponse
    {
        $qaTest->steps()->delete();
        $qaTest->delete();

        return redirect()->route('quality.qa-testing')->with('success', 'Test case removed.');
    }
}
